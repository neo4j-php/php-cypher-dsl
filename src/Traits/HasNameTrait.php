<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use function bin2hex;
use function ceil;
use function openssl_random_pseudo_bytes;
use function substr;

trait HasNameTrait
{
    public static function automaticVariableLength(): int {
        return 32;
    }

    /**
     * Generates a unique random identifier.
     *
     * @note It is not entirely guaranteed that this function gives a truly unique identifier. However, because the
     * number of possible IDs is so huge, it should not be a problem.
     *
     * @param int|null $length
     *
     * @return string
     */
    public static function generateUUID(int $length = null): string
    {
        $length ??= self::automaticVariableLength();

        return substr(bin2hex(openssl_random_pseudo_bytes(ceil($length / 2))), 0, $length);
    }
}