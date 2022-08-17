<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\IntegerTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use TypeError;

/**
 * Represents an integer literal.
 */
final class Integer implements IntegerType
{
    use IntegerTypeTrait;

    /**
     * @var string The value
     */
    private string $value;

    /**
     * @param int|string $value The value
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct($value)
    {
        if (!is_int($value) && !is_string($value)) {
            throw new TypeError('An integer should be given as a string or integer, '.gettype($value).' received.');
        }
        $parsedValue = filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        if (false === $parsedValue) {
           throw new TypeError(
               'A non-integer string has been provided: "'.$value.'".'
           );
        } elseif (is_string($value) && $parsedValue !== $value) {
           throw new TypeError(
               'A non-integer string has been provided: "'.$value.'", should be something like "'.$parsedValue.'".'
           );
        }

        $this->value = $parsedValue;
    }

    /**
     * Returns a string representation of the integer value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return $this->value;
    }
}
