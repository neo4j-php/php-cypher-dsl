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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Map
 */
class PropertyMapTest extends TestCase
{
    use TestHelper;

    public function testEmpty()
    {
        $propertyMap = new Map([]);

        $this->assertSame("{}", $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testNumericalKeys(array $properties, string $expected)
    {
        $propertyMap = new Map($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testStringKeys(array $properties, string $expected)
    {
        $propertyMap = new Map($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNestedPropertyMapsData
     * @param array $properties
     * @param string $expected
     */
    public function testNestedPropertyMaps(array $properties, string $expected)
    {
        $propertyMap = new Map($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    public function testMergeWith()
    {
        $propertyMap = new Map(["foo" => new String_("bar")]);
        $propertyMap->mergeWith(new Map(["boo" => new String_("far")]));

        $this->assertSame("{foo: 'bar', boo: 'far'}", $propertyMap->toQuery());

        $propertyMap->mergeWith($propertyMap);

        $this->assertSame("{foo: 'bar', boo: 'far'}", $propertyMap->toQuery());
    }

    public function testAddProperty()
    {
        $propertyMap = new Map(["foo" => new String_("bar")]);
        $propertyMap->addProperty('foo', new String_("baz"));

        $this->assertSame("{foo: 'baz'}", $propertyMap->toQuery());

        $propertyMap->addProperty('boo', new String_("far"));

        $this->assertSame("{foo: 'baz', boo: 'far'}", $propertyMap->toQuery());

        $propertyMap->addProperty('boo', false);

        $this->assertSame("{foo: 'baz', boo: false}", $propertyMap->toQuery());
    }

    public function provideNumericalKeysData(): array
    {
        return [
            [[$this->getQueryConvertibleMock(AnyType::class, "'a'")], "{`0`: 'a'}"],
            [[$this->getQueryConvertibleMock(AnyType::class, "'a'"), $this->getQueryConvertibleMock(AnyType::class, "'b'")], "{`0`: 'a', `1`: 'b'}"],
        ];
    }

    public function provideStringKeysData(): array
    {
        return [
            [['a' => $this->getQueryConvertibleMock(AnyType::class, "'a'")], "{a: 'a'}"],
            [['a' => $this->getQueryConvertibleMock(AnyType::class, "'a'"), 'b' => $this->getQueryConvertibleMock(AnyType::class, "'b'")], "{a: 'a', b: 'b'}"],
            [['a' => $this->getQueryConvertibleMock(AnyType::class, "'b'")], "{a: 'b'}"],
            [[':' => $this->getQueryConvertibleMock(AnyType::class, "'a'")], "{`:`: 'a'}"],
        ];
    }

    public function provideNestedPropertyMapsData()
    {
        return [
            [['a' => new Map([])], "{a: {}}"],
            [['a' => new Map(['a' => new Map(['a' => $this->getQueryConvertibleMock(AnyType::class, "'b'")])])], "{a: {a: {a: 'b'}}}"],
            [['a' => new Map(['b' => $this->getQueryConvertibleMock(AnyType::class, "'c'")]), 'b' => $this->getQueryConvertibleMock(AnyType::class, "'d'")], "{a: {b: 'c'}, b: 'd'}"],
        ];
    }
}
