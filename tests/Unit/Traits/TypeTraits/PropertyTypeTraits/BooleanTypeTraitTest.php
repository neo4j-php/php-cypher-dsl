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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits\PropertyTypeTraits;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait
 */
class BooleanTypeTraitTest extends TestCase
{
    /**
     * @var MockObject|BooleanType
     */
    private $a;

    /**
     * @var MockObject|BooleanType
     */
    private $b;

    public function setUp(): void
    {
        $this->a = new class () implements BooleanType {
            use BooleanTypeTrait;

            public function toQuery(): string
            {
                return '';
            }
        };
        $this->b = new Boolean(false);
    }

    public function testAnd(): void
    {
        $and = $this->a->and($this->b);

        $this->assertInstanceOf(Conjunction::class, $and);

        $this->assertTrue($and->insertsParentheses());
        $this->assertEquals($this->a, $and->getLeft());
        $this->assertEquals($this->b, $and->getRight());
    }

    public function testAndNoParentheses(): void
    {
        $and = $this->a->and($this->b, false);

        $this->assertInstanceOf(Conjunction::class, $and);

        $this->assertFalse($and->insertsParentheses());
        $this->assertEquals($this->a, $and->getLeft());
        $this->assertEquals($this->b, $and->getRight());
    }

    public function testOr(): void
    {
        $or = $this->a->or($this->b);

        $this->assertInstanceOf(Disjunction::class, $or);

        $this->assertTrue($or->insertsParentheses());
        $this->assertEquals($this->a, $or->getLeft());
        $this->assertEquals($this->b, $or->getRight());
    }

    public function testOrNoParentheses(): void
    {
        $or = $this->a->or($this->b, false);

        $this->assertInstanceOf(Disjunction::class, $or);

        $this->assertFalse($or->insertsParentheses());
        $this->assertEquals($this->a, $or->getLeft());
        $this->assertEquals($this->b, $or->getRight());
    }

    public function testXor(): void
    {
        $xor = $this->a->xor($this->b);

        $this->assertInstanceOf(ExclusiveDisjunction::class, $xor);

        $this->assertTrue($xor->insertsParentheses());
        $this->assertEquals($this->a, $xor->getLeft());
        $this->assertEquals($this->b, $xor->getRight());
    }

    public function testXorNoParentheses(): void
    {
        $xor = $this->a->xor($this->b, false);

        $this->assertInstanceOf(ExclusiveDisjunction::class, $xor);

        $this->assertFalse($xor->insertsParentheses());
        $this->assertEquals($this->a, $xor->getLeft());
        $this->assertEquals($this->b, $xor->getRight());
    }

    public function testNot(): void
    {
        $not = $this->a->not();

        $this->assertInstanceOf(Negation::class, $not);
    }
}
