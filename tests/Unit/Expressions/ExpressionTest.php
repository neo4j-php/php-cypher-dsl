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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Addition;
use WikibaseSolutions\CypherDSL\Expressions\AndOperator;
use WikibaseSolutions\CypherDSL\Expressions\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Division;
use WikibaseSolutions\CypherDSL\Expressions\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Minus;
use WikibaseSolutions\CypherDSL\Expressions\Modulo;
use WikibaseSolutions\CypherDSL\Expressions\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\OrOperator;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMutation;
use WikibaseSolutions\CypherDSL\Expressions\StartsWith;
use WikibaseSolutions\CypherDSL\Expressions\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\XorOperator;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Expression
 */
class ExpressionTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|Expression
     */
    private Expression $a;

    /**
     * @var MockObject|Expression
     */
    private Expression $b;

    public function setUp(): void
    {
        parent::setUp();

        $this->a = $this->getExpressionMock("a", $this);
        $this->b = $this->getExpressionMock("b", $this);
    }

    public function testPlus()
    {
        $this->assertInstanceOf(Addition::class, $this->a->plus($this->b));
    }

    public function testAnd()
    {
        $this->assertInstanceOf(AndOperator::class, $this->a->and($this->b));
    }

    public function testContains()
    {
        $this->assertInstanceOf(Contains::class, $this->a->contains($this->b));
    }

    public function testDivide()
    {
        $this->assertInstanceOf(Division::class, $this->a->divide($this->b));
    }

    public function testEndsWith()
    {
        $this->assertInstanceOf(EndsWith::class, $this->a->endsWith($this->b));
    }

    public function testEquals()
    {
        $this->assertInstanceOf(Equality::class, $this->a->equals($this->b));
    }

    public function testExponentiate()
    {
        $this->assertInstanceOf(Exponentiation::class, $this->a->exponentiate($this->b));
    }

    public function testGt()
    {
        $this->assertInstanceOf(GreaterThan::class, $this->a->gt($this->b));
    }

    public function testGte()
    {
        $this->assertInstanceOf(GreaterThanOrEqual::class, $this->a->gte($this->b));
    }

    public function testNotEquals()
    {
        $this->assertInstanceOf(Inequality::class, $this->a->notEquals($this->b));
    }

    public function testLt()
    {
        $this->assertInstanceOf(LessThan::class, $this->a->lt($this->b));
    }

    public function testLte()
    {
        $this->assertInstanceOf(LessThanOrEqual::class, $this->a->lte($this->b));
    }

    public function testMod()
    {
        $this->assertInstanceOf(Modulo::class, $this->a->mod($this->b));
    }

    public function testTimes()
    {
        $this->assertInstanceOf(Multiplication::class, $this->a->times($this->b));
    }

    public function testOr()
    {
        $this->assertInstanceOf(OrOperator::class, $this->a->or($this->b));
    }

    public function testPlusEquals()
    {
        $this->assertInstanceOf(PropertyMutation::class, $this->a->plusEquals($this->b));
    }

    public function testStartsWith()
    {
        $this->assertInstanceOf(StartsWith::class, $this->a->startsWith($this->b));
    }

    public function testMinus()
    {
        $this->assertInstanceOf(Subtraction::class, $this->a->minus($this->b));
    }

    public function testXor()
    {
        $this->assertInstanceOf(XorOperator::class, $this->a->xor($this->b));
    }

    public function testProperty()
    {
        $this->assertInstanceOf(Property::class, $this->a->property("b"));
    }

    public function testNegate()
    {
        $this->assertInstanceOf(Minus::class, $this->a->negate());
    }
}