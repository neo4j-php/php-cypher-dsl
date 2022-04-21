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
use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\In;
use WikibaseSolutions\CypherDSL\Inequality;
use WikibaseSolutions\CypherDSL\IsNotNull;
use WikibaseSolutions\CypherDSL\IsNull;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Traits\PropertyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\PropertyTypeTrait
 */
class PropertyTypeTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|PropertyType
     */
    private $a;

    /**
     * @var MockObject|PropertyType
     */
    private $b;

    /**
     * @var MockObject|ListType
     */
    private $list;

    public function setUp(): void
    {
        $this->a = new class () implements PropertyType {
            use PropertyTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = $this->getQueryConvertableMock(PropertyType::class, "15");
        $this->list = $this->getQueryConvertableMock(ListType::class, "['foobar']");
    }

    public function testEquals(): void
    {
        $equals = $this->a->equals($this->b);

        $this->assertInstanceOf(Equality::class, $equals);

        $this->assertTrue($equals->insertsParentheses());
        $this->assertEquals($this->a, $equals->getLeft());
        $this->assertEquals($this->b, $equals->getRight());
    }

    public function testEqualsNoParentheses(): void
    {
        $equals = $this->a->equals($this->b, false);

        $this->assertInstanceOf(Equality::class, $equals);

        $this->assertFalse($equals->insertsParentheses());
        $this->assertEquals($this->a, $equals->getLeft());
        $this->assertEquals($this->b, $equals->getRight());
    }

    public function testNotEquals(): void
    {
        $notEquals = $this->a->notEquals($this->b);

        $this->assertInstanceOf(Inequality::class, $notEquals);

        $this->assertTrue($notEquals->insertsParentheses());
        $this->assertEquals($this->a, $notEquals->getLeft());
        $this->assertEquals($this->b, $notEquals->getRight());
    }

    public function testNotEqualsNoParentheses(): void
    {
        $notEquals = $this->a->notEquals($this->b, false);

        $this->assertInstanceOf(Inequality::class, $notEquals);

        $this->assertFalse($notEquals->insertsParentheses());
        $this->assertEquals($this->a, $notEquals->getLeft());
        $this->assertEquals($this->b, $notEquals->getRight());
    }

    public function testIn(): void
    {
        $in = $this->a->in($this->list);

        $this->assertInstanceOf(In::class, $in);

        $this->assertTrue($in->insertsParentheses());
        $this->assertEquals($this->a, $in->getLeft());
        $this->assertEquals($this->list, $in->getRight());
    }

    public function testInNoParentheses(): void
    {
        $in = $this->a->in($this->list, false);

        $this->assertInstanceOf(In::class, $in);

        $this->assertFalse($in->insertsParentheses());
        $this->assertEquals($this->a, $in->getLeft());
        $this->assertEquals($this->list, $in->getRight());
    }

    public function testIsNull(): void
    {
        $test = $this->a->isNull();

        $this->assertInstanceOf(IsNull::class, $test);

        $this->assertEquals($this->a, $test->getExpression());
    }

    public function testIsNotNull(): void
    {
        $test = $this->a->isNotNull();

        $this->assertInstanceOf(IsNotNull::class, $test);

        $this->assertEquals($this->a, $test->getExpression());
    }
}
