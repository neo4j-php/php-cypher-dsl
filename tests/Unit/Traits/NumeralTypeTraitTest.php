<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Addition;
use WikibaseSolutions\CypherDSL\Division;
use WikibaseSolutions\CypherDSL\Exponentiation;
use WikibaseSolutions\CypherDSL\GreaterThan;
use WikibaseSolutions\CypherDSL\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\LessThan;
use WikibaseSolutions\CypherDSL\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Minus;
use WikibaseSolutions\CypherDSL\Modulo;
use WikibaseSolutions\CypherDSL\Multiplication;
use WikibaseSolutions\CypherDSL\Subtraction;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait
 */
class NumeralTypeTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|NumeralType
     */
    private $a;

    /**
     * @var MockObject|NumeralType
     */
    private $b;

    public function setUp(): void
    {
        $this->a = $this->getQueryConvertableMock(NumeralType::class, "10");
        $this->b = $this->getQueryConvertableMock(NumeralType::class, "15");
    }

    public function testPlus()
    {
        $plus = $this->a->plus($this->b);

        $this->assertInstanceOf(Addition::class, $plus);
    }

    public function testDivide()
    {
        $divide = $this->a->divide($this->b);

        $this->assertInstanceOf(Division::class, $divide);
    }

    public function testExponentiate()
    {
        $exponentiate = $this->a->exponentiate($this->b);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);
    }

    public function testGt()
    {
        $gt = $this->a->gt($this->b);

        $this->assertInstanceOf(GreaterThan::class, $gt);
    }

    public function testGte()
    {
        $gte = $this->a->gte($this->b);

        $this->assertInstanceOf(GreaterThanOrEqual::class, $gte);
    }

    public function testLt()
    {
        $lt = $this->a->lt($this->b);

        $this->assertInstanceOf(LessThan::class, $lt);
    }

    public function testLte()
    {
        $lte = $this->a->lte($this->b);

        $this->assertInstanceOf(LessThanOrEqual::class, $lte);
    }

    public function testMod()
    {
        $mod = $this->a->mod($this->b);

        $this->assertInstanceOf(Modulo::class, $mod);
    }

    public function testTimes()
    {
        $times = $this->a->times($this->b);

        $this->assertInstanceOf(Multiplication::class, $times);
    }

    public function testMinus()
    {
        $minus = $this->a->minus($this->b);

        $this->assertInstanceOf(Subtraction::class, $minus);
    }

    public function testNegate()
    {
        $negate = $this->a->negate();

        $this->assertInstanceOf(Minus::class, $negate);
    }
}