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
use WikibaseSolutions\CypherDSL\GreaterThan;
use WikibaseSolutions\CypherDSL\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\LessThan;
use WikibaseSolutions\CypherDSL\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\ComparablePropertyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\NumeralTypeTrait
 */
class ComparableTypeTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|ComparablePropertyType
     */
    private $a;

    /**
     * @var MockObject|ComparablePropertyType
     */
    private $b;

    public function setUp(): void
    {
        $this->a = new class () implements ComparablePropertyType {
            use ComparablePropertyTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = $this->getQueryConvertibleMock(ComparablePropertyType::class, "date({year: 2020, month: 12, day: 5})");
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
}
