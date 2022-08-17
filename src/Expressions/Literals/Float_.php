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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\FloatTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;

/**
 * Represents a float literal.
 */
final class Float_ implements FloatType
{
    use FloatTypeTrait;

    /**
     * @var float The value
     */
    private float $value;

    /**
     * @param float $value The value
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the integer value.
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $value = (string) $this->value;
        if (
            ctype_digit($value)
            || ($value[0] === '-' && ctype_digit(substr($value,1)) )
        ) {
            $value .= '.0';
        }
        return $value;
    }
}
