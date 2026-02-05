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
 */
trait NumeralTypeTrait
{
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function plus(NumeralType|float|int $right): Addition
    {
        return new Addition($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function divide(NumeralType|float|int $right): Division
    {
        return new Division($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function exponentiate(NumeralType|float|int $right): Exponentiation
    {
        return new Exponentiation($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function mod(NumeralType|float|int $right): ModuloDivision
    {
        return new ModuloDivision($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function times(NumeralType|float|int $right): Multiplication
    {
        return new Multiplication($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function minus(NumeralType|float|int $right): Subtraction
    {
        return new Subtraction($this, CastUtils::toNumeralType($right));
    }

    /**
     * @inheritDoc
     */
    public function negate(): UnaryMinus
    {
        return new UnaryMinus($this);
    }
}
