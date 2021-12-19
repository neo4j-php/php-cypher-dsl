<?php

namespace WikibaseSolutions\CypherDSL\Literals;

use TypeError;
use WikibaseSolutions\CypherDSL\ExpressionList;
use function get_class;
use function gettype;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;

abstract class Literal
{
    public static function boolean(bool $boolean): Boolean
    {
        return new Boolean($boolean);
    }

    /**
     * @param int|float $decimal
     */
    public static function decimal($decimal): Decimal
    {
        return new Decimal($decimal);
    }

    public static function string(string $string): StringLiteral
    {
        return new StringLiteral($string);
    }

    /**
     * Constructs a literal from a PHP literal.
     *
     * @param mixed $literal
     */
    public static function fromLiteral($literal): self
    {
        if (is_string($literal) || (is_object($literal) && method_exists($literal, '__toString'))) {
            return new StringLiteral((string) $literal);
        }

        if (is_bool($literal)) {
            return new Boolean($literal);
        }

        if (is_int($literal) || is_float($literal)) {
            return new Decimal($literal);
        }

        $actualType = is_object($literal) ? get_class($literal) : gettype($literal);
        throw new TypeError(sprintf('Cannot create a literal from: "%s".', $actualType));
    }
}