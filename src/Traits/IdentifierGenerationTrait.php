<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Patterns\Node;

trait IdentifierGenerationTrait
{
    /**
     * Cast the given value to a Variable if it is a node.
     *
     * @param mixed $value
     * @return mixed
     */
    public function variableIfNode($value)
    {
        if (!($value instanceof Node)) {
            return $value;
        }

        if (!$value->hasName()) {
            $value->setName($this->generateUUID());
        }

        return $value->getName();
    }

    /**
     * Generates a unique random identifier.
     *
     * @note It is not entirely guaranteed that this function gives a truly unique identifier. However, because the
     * number of possible IDs is so huge, it should not be a problem.
     *
     * @param int $length
     * @return string
     */
    private function generateUUID(int $length = 32): string
    {
        return substr(bin2hex(openssl_random_pseudo_bytes(ceil($length / 2))), 0, $length);
    }
}