<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Types\PropertyTypes;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ModuloDivision;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;

/**
 * Represents the abstract type "numeral", which has the subtypes "float" and "integer".
 *
 * @note This interface should not be implemented by any class directly
 *
 * @see NumeralTypeTrait for a default implementation
 */
interface NumeralType extends PropertyType
{
    /**
     * Add this expression to the given expression.
     */
    public function plus(NumeralType|float|int $right, bool $insertParentheses = true): Addition;

    /**
     * Divide this expression by the given expression.
     */
    public function divide(NumeralType|float|int $right, bool $insertParentheses = true): Division;

    /**
     * Perform an exponentiation with the given expression.
     */
    public function exponentiate(NumeralType|float|int $right, bool $insertParentheses = true): Exponentiation;

    /**
     * Perform the modulo operation with the given expression.
     */
    public function mod(NumeralType|float|int $right, bool $insertParentheses = true): ModuloDivision;

    /**
     * Perform a multiplication with the given expression.
     */
    public function times(NumeralType|float|int $right, bool $insertParentheses = true): Multiplication;

    /**
     * Subtract the given expression from this expression.
     */
    public function minus(NumeralType|float|int $right, bool $insertParentheses = true): Subtraction;

    /**
     * Negate this expression (negate the numeral using "0").
     */
    public function negate(): UnaryMinus;
}
