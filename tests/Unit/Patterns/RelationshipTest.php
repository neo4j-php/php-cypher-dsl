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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Patterns\Path
 */
class RelationshipTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|StructuralType
     */
    private StructuralType $a;

    /**
     * @var MockObject|StructuralType
     */
    private StructuralType $b;

    public function setUp(): void
    {
        $this->a = $this->getQueryConvertableMock(Node::class, "(a)");
        $this->b = $this->getQueryConvertableMock(Node::class, "(b)");
    }

    public function testDirRight()
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $this->assertSame("(a)-[]->(b)", $r->toQuery());
    }

    public function testDirLeft()
    {
        $r = new Path($this->a, $this->b, Path::DIR_LEFT);
        $this->assertSame("(a)<-[]-(b)", $r->toQuery());
    }

    public function testDirUni()
    {
        $r = new Path($this->a, $this->b, Path::DIR_UNI);
        $this->assertSame("(a)-[]-(b)", $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameData
     * @param        string $name
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithName(string $name, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithTypeData
     * @param        string $type
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithType(string $type, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withType($type);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithPropertiesData
     * @param        array  $properties
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithProperties(array $properties, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndTypeData
     * @param        string $name
     * @param        string $type
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithNameAndType(string $name, string $type, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withType($type);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndPropertiesData
     * @param        string $name
     * @param        array  $properties
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithNameAndProperties(string $name, array $properties, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithTypeAndPropertiesData
     * @param        string $type
     * @param        array  $properties
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithTypeAndProperties(string $type, array $properties, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withType($type)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndTypeAndPropertiesData
     * @param        string $name
     * @param        string $type
     * @param        array  $properties
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithNameAndTypeAndProperties(string $name, string $type, array $properties, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withType($type)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithMultipleTypesData
     * @param        string $name
     * @param        array  $types
     * @param        array  $properties
     * @param        array  $direction
     * @param        string $expected
     */
    public function testWithMultipleTypes(string $name, array $types, array $properties, array $direction, string $expected)
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withProperties($properties);

        foreach ($types as $type) {
            $r->withType($type);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    public function provideWithNameData(): array
    {
        return [
        ['', Path::DIR_UNI, '(a)-[]-(b)'],
        ['a', Path::DIR_UNI, '(a)-[a]-(b)'],
        ['a', Path::DIR_LEFT, '(a)<-[a]-(b)'],
        [':', Path::DIR_RIGHT, '(a)-[`:`]->(b)']
        ];
    }

    public function provideWithTypeData(): array
    {
        return [
        ['', Path::DIR_LEFT, '(a)<-[]-(b)'],
        ['a', Path::DIR_LEFT, '(a)<-[:a]-(b)'],
        [':', Path::DIR_LEFT, '(a)<-[:`:`]-(b)']
        ];
    }

    public function provideWithPropertiesData(): array
    {
        return [
        [[], Path::DIR_LEFT, "(a)<-[{}]-(b)"],
        [[new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[{`0`: 'a'}]-(b)"],
        [['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
        [['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[{a: 'b', `0`: 'c'}]-(b)"],
        [[':' => new Decimal(12)], Path::DIR_LEFT, "(a)<-[{`:`: 12}]-(b)"]
        ];
    }

    public function provideWithNameAndTypeData(): array
    {
        return [
        ['', '', Path::DIR_LEFT, '(a)<-[]-(b)'],
        ['a', '', Path::DIR_LEFT, '(a)<-[a]-(b)'],
        ['', 'a', Path::DIR_LEFT, '(a)<-[:a]-(b)'],
        ['a', 'b', Path::DIR_LEFT, '(a)<-[a:b]-(b)'],
        [':', 'b', Path::DIR_LEFT, '(a)<-[`:`:b]-(b)'],
        [':', ':', Path::DIR_LEFT, '(a)<-[`:`:`:`]-(b)']
        ];
    }

    public function provideWithNameAndPropertiesData(): array
    {
        return [
        ['a', [], Path::DIR_LEFT, "(a)<-[a {}]-(b)"],
        ['b', [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[b {`0`: 'a'}]-(b)"],
        ['', ['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
        [':', ['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[`:` {a: 'b', `0`: 'c'}]-(b)"]
        ];
    }

    public function provideWithTypeAndPropertiesData(): array
    {
        return [
        ['a', [], Path::DIR_LEFT, "(a)<-[:a {}]-(b)"],
        ['b', [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[:b {`0`: 'a'}]-(b)"],
        ['', ['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
        [':', ['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[:`:` {a: 'b', `0`: 'c'}]-(b)"]
        ];
    }

    public function provideWithNameAndTypeAndPropertiesData(): array
    {
        return [
        ['a', 'a', [], Path::DIR_LEFT, "(a)<-[a:a {}]-(b)"],
        ['b', 'a', [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[b:a {`0`: 'a'}]-(b)"],
        ['', 'a', ['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[:a {a: 'b'}]-(b)"],
        [':', 'a', ['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[`:`:a {a: 'b', `0`: 'c'}]-(b)"],
        ['a', 'b', [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[a:b {`0`: 'a'}]-(b)"],
        ['a', '', ['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[a {a: 'b'}]-(b)"],
        ['a', ':', ['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[a:`:` {a: 'b', `0`: 'c'}]-(b)"]
        ];
    }

    public function provideWithMultipleTypesData(): array
    {
        return [
        ['a', [], [], Path::DIR_LEFT, "(a)<-[a {}]-(b)"],
        ['b', ['a'], [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[b:a {`0`: 'a'}]-(b)"],
        ['', ['a', 'b'], ['a' => new StringLiteral('b')], Path::DIR_LEFT, "(a)<-[:a|b {a: 'b'}]-(b)"],
        [':', ['a', ':'], ['a' => new StringLiteral('b'), new StringLiteral('c')], Path::DIR_LEFT, "(a)<-[`:`:a|`:` {a: 'b', `0`: 'c'}]-(b)"],
        ['a', ['a', 'b', 'c'], [new StringLiteral('a')], Path::DIR_LEFT, "(a)<-[a:a|b|c {`0`: 'a'}]-(b)"],
        ['a', ['a', 'b'], [], Path::DIR_LEFT, "(a)<-[a:a|b {}]-(b)"]
        ];
    }
}