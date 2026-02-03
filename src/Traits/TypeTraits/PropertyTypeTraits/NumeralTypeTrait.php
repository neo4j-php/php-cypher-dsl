<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ModuloDivision;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This trait provides a default implementation to satisfy the "NumeralType" interface.
 *
 * @implements NumeralType
 */
trait NumeralTypeTrait
{
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function plus(NumeralType|float|int $right, bool $insertParentheses = true): Addition
    {
        return new Addition($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function divide(NumeralType|float|int $right, bool $insertParentheses = true): Division
    {
        return new Division($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function exponentiate(NumeralType|float|int $right, bool $insertParentheses = true): Exponentiation
    {
        return new Exponentiation($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function mod(NumeralType|float|int $right, bool $insertParentheses = true): ModuloDivision
    {
        return new ModuloDivision($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function times(NumeralType|float|int $right, bool $insertParentheses = true): Multiplication
    {
        return new Multiplication($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function minus(NumeralType|float|int $right, bool $insertParentheses = true): Subtraction
    {
        return new Subtraction($this, CastUtils::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function negate(): UnaryMinus
    {
        return new UnaryMinus($this);
    }
}
