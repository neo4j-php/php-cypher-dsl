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
use WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait;
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
        $this->a = new class implements NumeralType {
            use NumeralTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = $this->getQueryConvertableMock(NumeralType::class, "15");
    }

    public function testPlus(): void
    {
        $plus = $this->a->plus($this->b);

        $this->assertInstanceOf(Addition::class, $plus);

        $this->assertTrue($plus->insertsParentheses());
        $this->assertEquals($this->a, $plus->getLeft());
        $this->assertEquals($this->b, $plus->getRight());
    }

    public function testPlusNoParentheses(): void
    {
        $plus = $this->a->plus($this->b, false);

        $this->assertInstanceOf(Addition::class, $plus);

        $this->assertFalse($plus->insertsParentheses());
        $this->assertEquals($this->a, $plus->getLeft());
        $this->assertEquals($this->b, $plus->getRight());
    }

    public function testDivide(): void
    {
        $divide = $this->a->divide($this->b);

        $this->assertInstanceOf(Division::class, $divide);

        $this->assertTrue($divide->insertsParentheses());
        $this->assertEquals($this->a, $divide->getLeft());
        $this->assertEquals($this->b, $divide->getRight());
    }

    public function testDivideNoParentheses(): void
    {
        $divide = $this->a->divide($this->b, false);

        $this->assertInstanceOf(Division::class, $divide);

        $this->assertFalse($divide->insertsParentheses());
        $this->assertEquals($this->a, $divide->getLeft());
        $this->assertEquals($this->b, $divide->getRight());
    }

    public function testExponentiate(): void
    {
        $exponentiate = $this->a->exponentiate($this->b);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);

        $this->assertTrue($exponentiate->insertsParentheses());
        $this->assertEquals($this->a, $exponentiate->getLeft());
        $this->assertEquals($this->b, $exponentiate->getRight());
    }

    public function testExponentiateNoParentheses(): void
    {
        $exponentiate = $this->a->exponentiate($this->b, false);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);

        $this->assertFalse($exponentiate->insertsParentheses());
        $this->assertEquals($this->a, $exponentiate->getLeft());
        $this->assertEquals($this->b, $exponentiate->getRight());
    }

    public function testGt(): void
    {
        $gt = $this->a->gt($this->b);

        $this->assertInstanceOf(GreaterThan::class, $gt);

        $this->assertTrue($gt->insertsParentheses());
        $this->assertEquals($this->a, $gt->getLeft());
        $this->assertEquals($this->b, $gt->getRight());
    }

    public function testGtNoParentheses(): void
    {
        $gt = $this->a->gt($this->b, false);

        $this->assertInstanceOf(GreaterThan::class, $gt);

        $this->assertFalse($gt->insertsParentheses());
        $this->assertEquals($this->a, $gt->getLeft());
        $this->assertEquals($this->b, $gt->getRight());
    }

    public function testGte(): void
    {
        $gte = $this->a->gte($this->b);

        $this->assertInstanceOf(GreaterThanOrEqual::class, $gte);

        $this->assertTrue($gte->insertsParentheses());
        $this->assertEquals($this->a, $gte->getLeft());
        $this->assertEquals($this->b, $gte->getRight());
    }

    public function testGteNoParentheses(): void
    {
        $gte = $this->a->gte($this->b, false);

        $this->assertInstanceOf(GreaterThanOrEqual::class, $gte);

        $this->assertFalse($gte->insertsParentheses());
        $this->assertEquals($this->a, $gte->getLeft());
        $this->assertEquals($this->b, $gte->getRight());
    }

    public function testLt(): void
    {
        $lt = $this->a->lt($this->b);

        $this->assertInstanceOf(LessThan::class, $lt);

        $this->assertTrue($lt->insertsParentheses());
        $this->assertEquals($this->a, $lt->getLeft());
        $this->assertEquals($this->b, $lt->getRight());
    }

    public function testLtNoParentheses(): void
    {
        $lt = $this->a->lt($this->b, false);

        $this->assertInstanceOf(LessThan::class, $lt);

        $this->assertFalse($lt->insertsParentheses());
        $this->assertEquals($this->a, $lt->getLeft());
        $this->assertEquals($this->b, $lt->getRight());
    }

    public function testLte(): void
    {
        $lte = $this->a->lte($this->b);

        $this->assertInstanceOf(LessThanOrEqual::class, $lte);

        $this->assertTrue($lte->insertsParentheses());
        $this->assertEquals($this->a, $lte->getLeft());
        $this->assertEquals($this->b, $lte->getRight());
    }

    public function testLteNoParentheses(): void
    {
        $lte = $this->a->lte($this->b, false);

        $this->assertInstanceOf(LessThanOrEqual::class, $lte);

        $this->assertFalse($lte->insertsParentheses());
        $this->assertEquals($this->a, $lte->getLeft());
        $this->assertEquals($this->b, $lte->getRight());
    }

    public function testMod(): void
    {
        $mod = $this->a->mod($this->b);

        $this->assertInstanceOf(Modulo::class, $mod);

        $this->assertTrue($mod->insertsParentheses());
        $this->assertEquals($this->a, $mod->getLeft());
        $this->assertEquals($this->b, $mod->getRight());
    }

    public function testModNoParentheses(): void
    {
        $mod = $this->a->mod($this->b, false);

        $this->assertInstanceOf(Modulo::class, $mod);

        $this->assertFalse($mod->insertsParentheses());
        $this->assertEquals($this->a, $mod->getLeft());
        $this->assertEquals($this->b, $mod->getRight());
    }

    public function testTimes(): void
    {
        $times = $this->a->times($this->b);

        $this->assertInstanceOf(Multiplication::class, $times);

        $this->assertTrue($times->insertsParentheses());
        $this->assertEquals($this->a, $times->getLeft());
        $this->assertEquals($this->b, $times->getRight());
    }

    public function testTimesNoParentheses(): void
    {
        $times = $this->a->times($this->b, false);

        $this->assertInstanceOf(Multiplication::class, $times);

        $this->assertFalse($times->insertsParentheses());
        $this->assertEquals($this->a, $times->getLeft());
        $this->assertEquals($this->b, $times->getRight());
    }

    public function testMinus(): void
    {
        $minus = $this->a->minus($this->b);

        $this->assertInstanceOf(Subtraction::class, $minus);

        $this->assertTrue($minus->insertsParentheses());
        $this->assertEquals($this->a, $minus->getLeft());
        $this->assertEquals($this->b, $minus->getRight());
    }

    public function testMinusNoParentheses(): void
    {
        $minus = $this->a->minus($this->b, false);

        $this->assertInstanceOf(Subtraction::class, $minus);

        $this->assertFalse($minus->insertsParentheses());
        $this->assertEquals($this->a, $minus->getLeft());
        $this->assertEquals($this->b, $minus->getRight());
    }

    public function testNegate(): void
    {
        $negate = $this->a->negate();

        $this->assertInstanceOf(Minus::class, $negate);
    }
}
