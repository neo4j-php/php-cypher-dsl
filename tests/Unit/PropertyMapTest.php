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

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\PropertyMap
 */
class PropertyMapTest extends TestCase
{
    use TestHelper;

    public function testEmpty()
    {
        $propertyMap = new PropertyMap([]);

        $this->assertSame("{}", $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testNumericalKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testStringKeys(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    /**
     * @dataProvider provideNestedPropertyMapsData
     * @param array $properties
     * @param string $expected
     */
    public function testNestedPropertyMaps(array $properties, string $expected)
    {
        $propertyMap = new PropertyMap($properties);

        $this->assertSame($expected, $propertyMap->toQuery());
    }

    public function testMergeWith()
    {
        $propertyMap = new PropertyMap(["foo" => new StringLiteral("bar")]);
        $propertyMap->mergeWith(new PropertyMap(["boo" => new StringLiteral("far")]));

        $this->assertSame("{foo: 'bar', boo: 'far'}", $propertyMap->toQuery());

        $propertyMap->mergeWith($propertyMap);

        $this->assertSame("{foo: 'bar', boo: 'far'}", $propertyMap->toQuery());
    }

    public function testAddProperty()
    {
        $propertyMap = new PropertyMap(["foo" => new StringLiteral("bar")]);
        $propertyMap->addProperty('foo', new StringLiteral("baz"));

        $this->assertSame("{foo: 'baz'}", $propertyMap->toQuery());

        $propertyMap->addProperty('boo', new StringLiteral("far"));

        $this->assertSame("{foo: 'baz', boo: 'far'}", $propertyMap->toQuery());
    }

    public function provideNumericalKeysData(): array
    {
        return [
            [[$this->getQueryConvertableMock(AnyType::class, "'a'")], "{`0`: 'a'}"],
            [[$this->getQueryConvertableMock(AnyType::class, "'a'"), $this->getQueryConvertableMock(AnyType::class, "'b'")], "{`0`: 'a', `1`: 'b'}"],
        ];
    }

    public function provideStringKeysData(): array
    {
        return [
            [['a' => $this->getQueryConvertableMock(AnyType::class, "'a'")], "{a: 'a'}"],
            [['a' => $this->getQueryConvertableMock(AnyType::class, "'a'"), 'b' => $this->getQueryConvertableMock(AnyType::class, "'b'")], "{a: 'a', b: 'b'}"],
            [['a' => $this->getQueryConvertableMock(AnyType::class, "'b'")], "{a: 'b'}"],
            [[':' => $this->getQueryConvertableMock(AnyType::class, "'a'")], "{`:`: 'a'}"],
        ];
    }

    public function provideNestedPropertyMapsData()
    {
        return [
            [['a' => new PropertyMap([])], "{a: {}}"],
            [['a' => new PropertyMap(['a' => new PropertyMap(['a' => $this->getQueryConvertableMock(AnyType::class, "'b'")])])], "{a: {a: {a: 'b'}}}"],
            [['a' => new PropertyMap(['b' => $this->getQueryConvertableMock(AnyType::class, "'c'")]), 'b' => $this->getQueryConvertableMock(AnyType::class, "'d'")], "{a: {b: 'c'}, b: 'd'}"],
        ];
    }

    public function testRequiresAnyTypeProperties()
    {
        $a = new class () {};

        $this->expectException(TypeError::class);

        new PropertyMap([$a]);
    }
}
