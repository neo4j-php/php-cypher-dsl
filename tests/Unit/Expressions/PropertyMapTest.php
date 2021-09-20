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
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\PropertyMap
 */
class PropertyMapTest extends TestCase
{
    public function testEmpty()
    {
        $propertyMap = new PropertyMap([]);

        $this->assertSame("{}", $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testNumericalKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testStringKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNestedPropertyMapsData
     * @param        array  $properties
     * @param        string $expected
     */
    public function testNestedPropertyMaps(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    public function provideNumericalKeysData(): array
    {
        return [
        [[$this->getExpressionMock("'a'")], "{`0`: 'a'}"],
        [[$this->getExpressionMock("'a'"), $this->getExpressionMock("'b'")], "{`0`: 'a', `1`: 'b'}"]
        ];
    }

    public function provideStringKeysData(): array
    {
        return [
        [['a' => $this->getExpressionMock("'a'")], "{a: 'a'}"],
        [['a' => $this->getExpressionMock("'a'"), 'b' => $this->getExpressionMock("'b'")], "{a: 'a', b: 'b'}"],
        [['a' => $this->getExpressionMock("'b'")], "{a: 'b'}"],
        [[':' => $this->getExpressionMock("'a'")], "{`:`: 'a'}"]
        ];
    }

    public function provideNestedPropertyMapsData()
    {
        return [
        [['a' => new PropertyMap([])], "{a: {}}"],
        [['a' => new PropertyMap(['a' => new PropertyMap(['a' => $this->getExpressionMock("'b'")])])], "{a: {a: {a: 'b'}}}"],
        [['a' => new PropertyMap(['b' => $this->getExpressionMock("'c'")]), 'b' => $this->getExpressionMock("'d'")], "{a: {b: 'c'}, b: 'd'}"]
        ];
    }

    /**
     * Returns a mock of the Expression class that returns the given string when toQuery() is called.
     *
     * @param  string $variable
     * @return Expression|MockObject
     */
    private function getExpressionMock(string $variable): Expression
    {
        $mock = $this->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}