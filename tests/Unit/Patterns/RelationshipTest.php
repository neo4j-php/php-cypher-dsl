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

use DomainException;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Query;
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

    public function testDirRight(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->assertSame("(a)-[]->(b)", $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals(Path::DIR_RIGHT, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testDirLeft(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_LEFT);

        $this->assertSame("(a)<-[]-(b)", $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals(Path::DIR_LEFT, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testDirUni(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_UNI);

        $this->assertSame("(a)-[]-(b)", $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals(Path::DIR_UNI, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithNameData
     * @param string $name
     * @param array $direction
     * @param string $expected
     */
    public function testWithName(string $name, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithTypeData
     * @param string $type
     * @param array $direction
     * @param string $expected
     */
    public function testWithType(string $type, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withType($type);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithPropertiesData
     * @param array $properties
     * @param array $direction
     * @param string $expected
     */
    public function testWithProperties(array $properties, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithNameAndTypeData
     * @param string $name
     * @param string $type
     * @param array $direction
     * @param string $expected
     */
    public function testWithNameAndType(string $name, string $type, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withType($type);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithNameAndPropertiesData
     * @param string $name
     * @param array $properties
     * @param array $direction
     * @param string $expected
     */
    public function testWithNameAndProperties(string $name, array $properties, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithTypeAndPropertiesData
     * @param string $type
     * @param array $properties
     * @param array $direction
     * @param string $expected
     */
    public function testWithTypeAndProperties(string $type, array $properties, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withType($type)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithNameAndTypeAndPropertiesData
     * @param string $name
     * @param string $type
     * @param array $properties
     * @param array $direction
     * @param string $expected
     */
    public function testWithNameAndTypeAndProperties(string $name, string $type, array $properties, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withType($type)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideWithMultipleTypesData
     * @param string $name
     * @param array $types
     * @param array $properties
     * @param array $direction
     * @param string $expected
     */
    public function testWithMultipleTypes(string $name, array $types, array $properties, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withProperties($properties);

        foreach ($types as $type) {
            $r->withType($type);
        }


        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals($types, $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithNameData
     * @param string $name
     * @param int|null $minHops
     * @param int|null $maxHops
     * @param array $direction
     * @param string $expected
     */
    public function testVariableLengthRelationshipsWithName(string $name, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertEquals($maxHops, $r->getMaxHops());
        $this->assertEquals($minHops, $r->getMinHops());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithTypeData
     * @param string $type
     * @param int|null $minHops
     * @param int|null $maxHops
     * @param array $direction
     * @param string $expected
     */
    public function testVariableLengthRelationshipsWithType(string $type, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withType($type);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertEquals($maxHops, $r->getMaxHops());
        $this->assertEquals($minHops, $r->getMinHops());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithPropertiesData
     * @param array $properties
     * @param int|null $minHops
     * @param int|null $maxHops
     * @param array $direction
     * @param string $expected
     */
    public function testVariableLengthRelationshipsWithProperties(array $properties, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->withProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNull($r->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertEquals($maxHops, $r->getMaxHops());
        $this->assertEquals($minHops, $r->getMinHops());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithNameAndTypeAndPropertiesData
     * @param string $name
     * @param string $type
     * @param array $properties
     * @param int|null $minHops
     * @param int|null $maxHops
     * @param array $direction
     * @param string $expected
     */
    public function testVariableLengthRelationshipsWithNameAndTypeAndProperties(string $name, string $type, array $properties, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Path($this->a, $this->b, $direction);
        $r->named($name)->withType($type)->withProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals($name, $r->getVariable()->getVariable());

        $this->assertNull($r->getExactHops());
        $this->assertEquals($maxHops, $r->getMaxHops());
        $this->assertEquals($minHops, $r->getMinHops());
    }

    public function testExactLengthRelationships(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->named("tom")
            ->withType("Person")
            ->withProperties(['name' => Query::literal('Tom Hanks')]);

        $r->withExactHops(10);

        $this->assertSame("(a)-[tom:Person*10 {name: 'Tom Hanks'}]->(b)", $r->toQuery());

        $this->assertEquals($this->a, $r->getLeft());
        $this->assertEquals($this->b, $r->getRight());
        $this->assertEquals(Path::DIR_RIGHT, $r->getDirection());
        $this->assertEquals(['Person'], $r->getTypes());
        $this->assertEquals(new PropertyMap(['name' => Query::literal('Tom Hanks')]), $r->getProperties());
        $this->assertNotNull($r->getVariable());
        $this->assertEquals('tom', $r->getVariable()->getVariable());

        $this->assertEquals(10, $r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testMinAndExactHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withMinHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testMaxAndExactHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withMaxHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testMinMaxAndExactHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withMinHops(1);
        $r->withMaxHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testExactAndMinHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withExactHops(1);

        $this->expectException(LogicException::class);

        $r->withMinHops(1);
    }

    public function testExactAndMaxHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withExactHops(1);

        $this->expectException(LogicException::class);

        $r->withMaxHops(1);
    }

    public function testMaxHopsLessThanMinHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withMinHops(100);

        $this->expectException(DomainException::class);

        $r->withMaxHops(1);
    }

    public function testMinHopsGreaterThanMaxHops(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);
        $r->withMaxHops(1);

        $this->expectException(DomainException::class);

        $r->withMinHops(100);
    }

    public function testMinHopsLessThanZero(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMinHops(-1);
    }

    public function testMaxHopsLessThanOne(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMaxHops(0);
    }

    public function testMaxHopsLessThanZero(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMaxHops(-1);
    }

    public function testExactHopsLessThanOne(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withExactHops(0);
    }

    public function testExactHopsLessThanZero(): void
    {
        $r = new Path($this->a, $this->b, Path::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withExactHops(-1);
    }

    public function provideVariableLengthRelationshipsWithNameData(): array
    {
        return [
            ['', 1, 100, Path::DIR_UNI, '(a)-[*1..100]-(b)'],
            ['a', 10, null, Path::DIR_UNI, '(a)-[a*10..]-(b)'],
            ['a', null, 10, Path::DIR_LEFT, '(a)<-[a*..10]-(b)'],
        ];
    }

    public function provideVariableLengthRelationshipsWithTypeData(): array
    {
        return [
            ['', 1, 100, Path::DIR_LEFT, '(a)<-[*1..100]-(b)'],
            ['a', 10, null, Path::DIR_LEFT, '(a)<-[:a*10..]-(b)'],
            [':', null, 10, Path::DIR_LEFT, '(a)<-[:`:`*..10]-(b)']
        ];
    }

    public function provideVariableLengthRelationshipsWithPropertiesData(): array
    {
        return [
            [[], 10, 100, Path::DIR_LEFT, "(a)<-[*10..100 {}]-(b)"],
            [[new StringLiteral('a')], 10, null, Path::DIR_LEFT, "(a)<-[*10.. {`0`: 'a'}]-(b)"],
            [['a' => new StringLiteral('b')], null, 10, Path::DIR_LEFT, "(a)<-[*..10 {a: 'b'}]-(b)"]
        ];
    }

    public function provideVariableLengthRelationshipsWithNameAndTypeAndPropertiesData(): array
    {
        return [
            ['a', 'a', [], 10, 100, Path::DIR_LEFT, "(a)<-[a:a*10..100 {}]-(b)"],
            ['b', 'a', [new StringLiteral('a')], null, 10, Path::DIR_LEFT, "(a)<-[b:a*..10 {`0`: 'a'}]-(b)"],
            ['', 'a', ['a' => new StringLiteral('b')], 10, null, Path::DIR_LEFT, "(a)<-[:a*10.. {a: 'b'}]-(b)"],
            [':', 'a', ['a' => new StringLiteral('b'), new StringLiteral('c')], null, null, Path::DIR_LEFT, "(a)<-[`:`:a {a: 'b', `0`: 'c'}]-(b)"],
            ['a', 'b', [new StringLiteral('a')], 10, 100, Path::DIR_LEFT, "(a)<-[a:b*10..100 {`0`: 'a'}]-(b)"],
            ['a', '', ['a' => new StringLiteral('b')], null, 10, Path::DIR_LEFT, "(a)<-[a*..10 {a: 'b'}]-(b)"],
            ['a', ':', ['a' => new StringLiteral('b'), new StringLiteral('c')], 10, null, Path::DIR_LEFT, "(a)<-[a:`:`*10.. {a: 'b', `0`: 'c'}]-(b)"]
        ];
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
