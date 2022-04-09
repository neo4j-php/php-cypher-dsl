<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use function bin2hex;
use function ceil;
use function openssl_random_pseudo_bytes;
use function substr;

trait HasNameTrait
{
    use ErrorTrait;

    private string $name;

    private static int $automaticVariableLength = 32;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            $this->configureName(null, 'var');
        }

        return $this->name;
    }

    /**
     * Generates a unique random identifier.
     *
     * @note It is not entirely guaranteed that this function gives a truly unique identifier. However, because the
     * number of possible IDs is so huge, it should not be a problem.
     *
     * @param string $prefix The prefix to put before the name. Must start with a letter to adhere to cypher namings.
     *
     * @param int|null $length The length of the generated name in bytes.
     *
     * @return string
     */
    private function generateName(string $prefix = 'var', int $length = null): string
    {
        $length ??= self::$automaticVariableLength;

        return $prefix . substr(bin2hex(openssl_random_pseudo_bytes(ceil($length / 2))), 0, $length);
    }

    private function configureName(?string $givenName, string $prefix, int $length = null): void
    {
        $name = $givenName ?? $this->generateName($prefix, $length);

        self::assertValidName($name);

        $this->name = $name;
    }
}