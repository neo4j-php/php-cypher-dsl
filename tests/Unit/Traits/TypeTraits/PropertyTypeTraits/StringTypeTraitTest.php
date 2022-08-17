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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait
 */
class StringTypeTraitTest extends TestCase
{

    /**
     * @var MockObject|StringType
     */
    private $a;

    /**
     * @var MockObject|StringType
     */
    private $b;

    public function setUp(): void
    {
        $this->a = new class () implements StringType {
            use StringTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = new String_('15');
    }

    public function testContains(): void
    {
        $contains = $this->a->contains($this->b);

        $this->assertInstanceOf(Contains::class, $contains);

        $this->assertTrue($contains->insertsParentheses());
        $this->assertEquals($this->a, $contains->getLeft());
        $this->assertEquals($this->b, $contains->getRight());
    }

    public function testContainsNoParentheses(): void
    {
        $contains = $this->a->contains($this->b, false);

        $this->assertInstanceOf(Contains::class, $contains);

        $this->assertFalse($contains->insertsParentheses());
        $this->assertEquals($this->a, $contains->getLeft());
        $this->assertEquals($this->b, $contains->getRight());
    }

    public function testEndsWith(): void
    {
        $endsWith = $this->a->endsWith($this->b);

        $this->assertInstanceOf(EndsWith::class, $endsWith);

        $this->assertTrue($endsWith->insertsParentheses());
        $this->assertEquals($this->a, $endsWith->getLeft());
        $this->assertEquals($this->b, $endsWith->getRight());
    }

    public function testEndsWithNoParentheses(): void
    {
        $endsWith = $this->a->endsWith($this->b, false);

        $this->assertInstanceOf(EndsWith::class, $endsWith);

        $this->assertFalse($endsWith->insertsParentheses());
        $this->assertEquals($this->a, $endsWith->getLeft());
        $this->assertEquals($this->b, $endsWith->getRight());
    }

    public function testStartsWith(): void
    {
        $startsWith = $this->a->startsWith($this->b);

        $this->assertInstanceOf(StartsWith::class, $startsWith);

        $this->assertTrue($startsWith->insertsParentheses());
        $this->assertEquals($this->a, $startsWith->getLeft());
        $this->assertEquals($this->b, $startsWith->getRight());
    }

    public function testStartsWithNoParentheses(): void
    {
        $startsWith = $this->a->startsWith($this->b, false);

        $this->assertInstanceOf(StartsWith::class, $startsWith);

        $this->assertFalse($startsWith->insertsParentheses());
        $this->assertEquals($this->a, $startsWith->getLeft());
        $this->assertEquals($this->b, $startsWith->getRight());
    }

    public function testRegex(): void
    {
        $regex = $this->a->regex($this->b);

        $this->assertInstanceOf(Regex::class, $regex);

        $this->assertTrue($regex->insertsParentheses());
        $this->assertEquals($this->a, $regex->getLeft());
        $this->assertEquals($this->b, $regex->getRight());
    }

    public function testRegexNoParentheses(): void
    {
        $regex = $this->a->regex($this->b, false);

        $this->assertInstanceOf(Regex::class, $regex);

        $this->assertFalse($regex->insertsParentheses());
        $this->assertEquals($this->a, $regex->getLeft());
        $this->assertEquals($this->b, $regex->getRight());
    }
}
