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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Patterns;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Patterns\Relationship
 */
class RelationshipTest extends TestCase
{
    use TestHelper;

    public function testDirRight(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->assertSame("-->", $r->toQuery());

        $this->assertEquals(Relationship::DIR_RIGHT, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertFalse($r->hasVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testDirLeft(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);

        $this->assertSame("<--", $r->toQuery());

        $this->assertEquals(Relationship::DIR_LEFT, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertFalse($r->hasVariable());

        $this->assertNull($r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testDirUni(): void
    {
        $r = new Relationship(Relationship::DIR_UNI);

        $this->assertSame("--", $r->toQuery());

        $this->assertEquals(Relationship::DIR_UNI, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->setVariable($name);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->withType($type);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->addProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->setVariable($name)->withType($type);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->setVariable($name)->addProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->withType($type)->addProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->setVariable($name)->withType($type)->addProperties($properties);

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->setVariable($name)->addProperties($properties);

        foreach ($types as $type) {
            $r->withType($type);
        }


        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals($types, $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->setVariable($name);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

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
        $r = new Relationship($direction);
        $r->withType($type);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertNull($r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->addProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertFalse($r->hasVariable());

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
        $r = new Relationship($direction);
        $r->setVariable($name)->withType($type)->addProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());

        $this->assertEquals($direction, $r->getDirection());
        $this->assertEquals([$type], $r->getTypes());
        $this->assertEquals(new PropertyMap($properties), $r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals($name, $r->getVariable()->getName());

        $this->assertNull($r->getExactHops());
        $this->assertEquals($maxHops, $r->getMaxHops());
        $this->assertEquals($minHops, $r->getMinHops());
    }

    public function testExactLengthRelationships(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->setVariable("tom")
            ->withType("Person")
            ->addProperties(['name' => Query::literal('Tom Hanks')]);

        $r->withExactHops(10);

        $this->assertSame("-[tom:Person*10 {name: 'Tom Hanks'}]->", $r->toQuery());

        $this->assertEquals(Relationship::DIR_RIGHT, $r->getDirection());
        $this->assertEquals(['Person'], $r->getTypes());
        $this->assertEquals(new PropertyMap(['name' => Query::literal('Tom Hanks')]), $r->getProperties());
        $this->assertTrue($r->hasVariable());
        $this->assertEquals('tom', $r->getVariable()->getName());

        $this->assertEquals(10, $r->getExactHops());
        $this->assertNull($r->getMaxHops());
        $this->assertNull($r->getMinHops());
    }

    public function testMinAndExactHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMinHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testMaxAndExactHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMaxHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testMinMaxAndExactHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMinHops(1);
        $r->withMaxHops(1);

        $this->expectException(LogicException::class);

        $r->withExactHops(1);
    }

    public function testExactAndMinHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withExactHops(1);

        $this->expectException(LogicException::class);

        $r->withMinHops(1);
    }

    public function testExactAndMaxHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withExactHops(1);

        $this->expectException(LogicException::class);

        $r->withMaxHops(1);
    }

    public function testMaxHopsLessThanMinHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMinHops(100);

        $this->expectException(DomainException::class);

        $r->withMaxHops(1);
    }

    public function testMinHopsGreaterThanMaxHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMaxHops(1);

        $this->expectException(DomainException::class);

        $r->withMinHops(100);
    }

    public function testMinHopsLessThanZero(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMinHops(-1);
    }

    public function testMaxHopsLessThanOne(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMaxHops(0);
    }

    public function testMaxHopsLessThanZero(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withMaxHops(-1);
    }

    public function testExactHopsLessThanOne(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withExactHops(0);
    }

    public function testExactHopsLessThanZero(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->expectException(DomainException::class);

        $r->withExactHops(-1);
    }

    public function provideVariableLengthRelationshipsWithNameData(): array
    {
        return [
            ['b', 1, 100, Relationship::DIR_UNI, '-[b*1..100]-'],
            ['a', 10, null, Relationship::DIR_UNI, '-[a*10..]-'],
            ['a', null, 10, Relationship::DIR_LEFT, '<-[a*..10]-'],
        ];
    }

    public function provideVariableLengthRelationshipsWithTypeData(): array
    {
        return [
            ['', 1, 100, Relationship::DIR_LEFT, '<-[*1..100]-'],
            ['a', 10, null, Relationship::DIR_LEFT, '<-[:a*10..]-'],
            [':', null, 10, Relationship::DIR_LEFT, '<-[:`:`*..10]-'],
        ];
    }

    public function provideVariableLengthRelationshipsWithPropertiesData(): array
    {
        return [
            [[], 10, 100, Relationship::DIR_LEFT, "<-[*10..100]-"],
            [[new StringLiteral('a')], 10, null, Relationship::DIR_LEFT, "<-[*10.. {`0`: 'a'}]-"],
            [['a' => new StringLiteral('b')], null, 10, Relationship::DIR_LEFT, "<-[*..10 {a: 'b'}]-"],
        ];
    }

    public function provideVariableLengthRelationshipsWithNameAndTypeAndPropertiesData(): array
    {
        return [
            ['a', 'a', [], 10, 100, Relationship::DIR_LEFT, "<-[a:a*10..100]-"],
            ['b', 'a', [new StringLiteral('a')], null, 10, Relationship::DIR_LEFT, "<-[b:a*..10 {`0`: 'a'}]-"],
            ['a', 'b', [new StringLiteral('a')], 10, 100, Relationship::DIR_LEFT, "<-[a:b*10..100 {`0`: 'a'}]-"],
            ['a', '', ['a' => new StringLiteral('b')], null, 10, Relationship::DIR_LEFT, "<-[a*..10 {a: 'b'}]-"],
            ['a', ':', ['a' => new StringLiteral('b'), new StringLiteral('c')], 10, null, Relationship::DIR_LEFT, "<-[a:`:`*10.. {a: 'b', `0`: 'c'}]-"],
        ];
    }

    public function provideWithNameData(): array
    {
        return [
            ['a', Relationship::DIR_UNI, '-[a]-'],
            ['a', Relationship::DIR_LEFT, '<-[a]-'],
        ];
    }

    public function provideWithTypeData(): array
    {
        return [
            ['', Relationship::DIR_LEFT, '<--'],
            ['a', Relationship::DIR_LEFT, '<-[:a]-'],
            [':', Relationship::DIR_LEFT, '<-[:`:`]-'],
        ];
    }

    public function provideWithPropertiesData(): array
    {
        return [
            [[], Relationship::DIR_LEFT, "<--"],
            [[new StringLiteral('a')], Relationship::DIR_LEFT, "<-[{`0`: 'a'}]-"],
            [['a' => new StringLiteral('b')], Relationship::DIR_LEFT, "<-[{a: 'b'}]-"],
            [['a' => new StringLiteral('b'), new StringLiteral('c')], Relationship::DIR_LEFT, "<-[{a: 'b', `0`: 'c'}]-"],
            [[':' => new Decimal(12)], Relationship::DIR_LEFT, "<-[{`:`: 12}]-"],
        ];
    }

    public function provideWithNameAndTypeData(): array
    {
        return [
            ['a', '', Relationship::DIR_LEFT, '<-[a]-'],
            ['a', 'b', Relationship::DIR_LEFT, '<-[a:b]-'],
        ];
    }

    public function provideWithNameAndPropertiesData(): array
    {
        return [
            ['a', [], Relationship::DIR_LEFT, "<-[a]-"],
            ['b', [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[b {`0`: 'a'}]-"],
        ];
    }

    public function provideWithTypeAndPropertiesData(): array
    {
        return [
            ['a', [], Relationship::DIR_LEFT, "<-[:a]-"],
            ['b', [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[:b {`0`: 'a'}]-"],
            ['', ['a' => new StringLiteral('b')], Relationship::DIR_LEFT, "<-[{a: 'b'}]-"],
            [':', ['a' => new StringLiteral('b'), new StringLiteral('c')], Relationship::DIR_LEFT, "<-[:`:` {a: 'b', `0`: 'c'}]-"],
        ];
    }

    public function provideWithNameAndTypeAndPropertiesData(): array
    {
        return [
            ['a', 'a', [], Relationship::DIR_LEFT, "<-[a:a]-"],
            ['b', 'a', [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[b:a {`0`: 'a'}]-"],
            ['a', 'b', [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[a:b {`0`: 'a'}]-"],
            ['a', '', ['a' => new StringLiteral('b')], Relationship::DIR_LEFT, "<-[a {a: 'b'}]-"],
            ['a', ':', ['a' => new StringLiteral('b'), new StringLiteral('c')], Relationship::DIR_LEFT, "<-[a:`:` {a: 'b', `0`: 'c'}]-"],
        ];
    }

    public function provideWithMultipleTypesData(): array
    {
        return [
            ['a', [], [], Relationship::DIR_LEFT, "<-[a]-"],
            ['b', ['a'], [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[b:a {`0`: 'a'}]-"],
            ['a', ['a', 'b', 'c'], [new StringLiteral('a')], Relationship::DIR_LEFT, "<-[a:a|b|c {`0`: 'a'}]-"],
            ['a', ['a', 'b'], [], Relationship::DIR_LEFT, "<-[a:a|b]-"],
        ];
    }
}
