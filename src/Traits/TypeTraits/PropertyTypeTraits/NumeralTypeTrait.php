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

/**
 * This trait provides a default implementation to satisfy the "NumeralType" interface.
 */
trait NumeralTypeTrait
{
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function plus($right, bool $insertParentheses = true): Addition
    {
        return new Addition($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function divide($right, bool $insertParentheses = true): Division
    {
        return new Division($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function exponentiate($right, bool $insertParentheses = true): Exponentiation
    {
        return new Exponentiation($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function mod($right, bool $insertParentheses = true): ModuloDivision
    {
        return new ModuloDivision($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function times($right, bool $insertParentheses = true): Multiplication
    {
        return new Multiplication($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function minus($right, bool $insertParentheses = true): Subtraction
    {
        return new Subtraction($this, self::toNumeralType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function negate(): UnaryMinus
    {
        return new UnaryMinus($this);
    }
}
