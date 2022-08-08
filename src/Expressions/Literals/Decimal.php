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

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a decimal (integer or float) literal.
 */
final class Decimal implements NumeralType
{
    use NumeralTypeTrait;

    use ErrorTrait;

    /**
     * @var int|float The value
     */
    private $value;

    /**
     * @param int|float $value The value
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct($value)
    {
        $this->assertClass('value', ['int', 'float'], $value);
        $this->value = $value;
    }

    /**
     * Returns the numeric value.
     *
     * @return int|float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return (string)$this->value;
    }
}
