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
class MapTest extends TestCase
{

    public function testEmpty()
    {
        $map = new Map([]);

        $this->assertSame("{}", $map->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testNumericalKeys(array $properties, string $expected)
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     * @param array $properties
     * @param string $expected
     */
    public function testStringKeys(array $properties, string $expected)
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    /**
     * @dataProvider provideNestedMapsData
     * @param array $properties
     * @param string $expected
     */
    public function testNestedMaps(array $properties, string $expected)
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    public function testMergeWith()
    {
        $map = new Map(["foo" => new String_("bar")]);
        $map->mergeWith(new Map(["boo" => new String_("far")]));

        $this->assertSame("{foo: 'bar', boo: 'far'}", $map->toQuery());

        $map->mergeWith($map);

        $this->assertSame("{foo: 'bar', boo: 'far'}", $map->toQuery());
    }

    public function testAdd()
    {
        $map = new Map(["foo" => new String_("bar")]);
        $map->add('foo', new String_("baz"));

        $this->assertSame("{foo: 'baz'}", $map->toQuery());

        $map->add('boo', new String_("far"));

        $this->assertSame("{foo: 'baz', boo: 'far'}", $map->toQuery());

        $map->add('boo', false);

        $this->assertSame("{foo: 'baz', boo: false}", $map->toQuery());
    }

    public function provideNumericalKeysData(): array
    {
        return [
            [[0 => new String_('a')], "{`0`: 'a'}"],
            [[0 => new String_('a'), 1 => new String_('b')], "{`0`: 'a', `1`: 'b'}"],
        ];
    }

    public function provideStringKeysData(): array
    {
        return [
            [['a' => new String_('a')], "{a: 'a'}"],
            [['a' => new String_('a'), 'b' => new String_('b')], "{a: 'a', b: 'b'}"],
            [['a' => new String_('b')], "{a: 'b'}"],
            [[':' => new String_('a')], "{`:`: 'a'}"],
        ];
    }

    public function provideNestedMapsData()
    {
        return [
            [['a' => new Map([])], "{a: {}}"],
            [['a' => new Map(['a' => new Map(['a' => new String_('b')])])], "{a: {a: {a: 'b'}}}"],
            [['a' => new Map(['b' => new String_('c')]), 'b' => new String_('d')], "{a: {b: 'c'}, b: 'd'}"],
        ];
    }
}
