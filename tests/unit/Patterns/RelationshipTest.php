<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use DomainException;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Patterns\Relationship
 */
final class RelationshipTest extends TestCase
{
    public function testExceptionIsThrownWhenInvalidDirection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $r = new Relationship(['--', '--']);
    }

    public function testDirRight(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);

        $this->assertSame("-->", $r->toQuery());
    }

    public function testDirLeft(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);

        $this->assertSame("<--", $r->toQuery());
    }

    public function testDirUni(): void
    {
        $r = new Relationship(Relationship::DIR_UNI);

        $this->assertSame("--", $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameData
     */
    public function testWithName(string $name, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideAddTypeData
     */
    public function testAddType(string $type, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->addType($type);

        $this->assertSame($expected, $r->toQuery());
    }

    public function testAddTypeMultiple(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $r->addType("a");
        $r->addType("b");
        $r->addType(":");

        $this->assertSame("<-[:a|b|`:`]-", $r->toQuery());
    }

    /**
     * @dataProvider provideWithTypesData
     */
    public function testWithTypes(array $types, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withTypes($types);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithPropertiesData
     */
    public function testWithProperties(array $properties, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndTypeData
     */
    public function testWithNameAndType(string $name, string $type, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name)->addType($type);

        $this->assertSame($expected, $r->toQuery());
    }

    public function testWithNameAndMultipleTypes(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $r->withVariable('a')->addType('a')->addType('b');

        $this->assertSame('<-[a:a|b]-', $r->toQuery());
    }

    public function testWithVariableActualVariable(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $r->withVariable(new Variable('a'));

        $this->assertSame('<-[a]-', $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndPropertiesData
     */
    public function testWithNameAndProperties(string $name, array $properties, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithNameAndTypeAndPropertiesData
     */
    public function testWithNameAndTypeAndProperties(string $name, string $type, array $properties, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name)->addType($type)->withProperties($properties);

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideWithMultipleTypesData
     */
    public function testWithMultipleTypes(string $name, array $types, array $properties, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name)->withProperties($properties);

        foreach ($types as $type) {
            $r->addType($type);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithNameData
     */
    public function testVariableLengthRelationshipsWithName(string $name, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithTypeData
     */
    public function testVariableLengthRelationshipsWithType(string $type, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->addType($type);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithPropertiesData
     */
    public function testVariableLengthRelationshipsWithProperties(array $properties, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    /**
     * @dataProvider provideVariableLengthRelationshipsWithNameAndTypeAndPropertiesData
     */
    public function testVariableLengthRelationshipsWithNameAndTypeAndProperties(string $name, string $type, array $properties, ?int $minHops, ?int $maxHops, array $direction, string $expected): void
    {
        $r = new Relationship($direction);
        $r->withVariable($name)->addType($type)->withProperties($properties);

        if (isset($minHops)) {
            $r->withMinHops($minHops);
        }

        if (isset($maxHops)) {
            $r->withMaxHops($maxHops);
        }

        $this->assertSame($expected, $r->toQuery());
    }

    public function testArbitraryHops(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $r->withVariable('hello')->addType('world')->addType('testing')->withProperties(['is' => 'a virtue']);
        $r->withArbitraryHops();

        $this->assertSame('<-[hello:world|testing* {is: \'a virtue\'}]-', $r->toQuery());
    }

    public function testExactLengthRelationships(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withVariable("tom")
            ->addType("Person")
            ->withProperties(['name' => 'Tom Hanks']);

        $r->withExactHops(10);

        $this->assertSame("-[tom:Person*10 {name: 'Tom Hanks'}]->", $r->toQuery());
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

    public function testExactHopsWithArbitraryHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withArbitraryHops();

        $this->expectException(LogicException::class);

        $r->withExactHops(5);
    }

    public function testMinHopsWithArbitraryHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withArbitraryHops();

        $this->expectException(LogicException::class);

        $r->withMinHops(5);
    }

    public function testMaxHopsWithArbitraryHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withArbitraryHops();

        $this->expectException(LogicException::class);

        $r->withMaxHops(5);
    }

    public function testArbitraryHopsWithExactHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withExactHops(5);

        $this->expectException(LogicException::class);

        $r->withArbitraryHops();
    }

    public function testArbitraryHopsWithMinHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMinHops(5);

        $this->expectException(LogicException::class);

        $r->withArbitraryHops();
    }

    public function testArbitraryHopsWithMaxHops(): void
    {
        $r = new Relationship(Relationship::DIR_RIGHT);
        $r->withMaxHops(5);

        $this->expectException(LogicException::class);

        $r->withArbitraryHops();
    }

    public function testGetDirection(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertSame(Relationship::DIR_LEFT, $r->getDirection());
    }

    public function testGetProperties(): void
    {
        $properties = Query::map(['foo' => 'bar']);
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertNull($r->getProperties());

        $r->withProperties($properties);

        $this->assertSame($properties, $r->getProperties());
    }

    public function testGetExactHops(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertNull($r->getExactHops());

        $r->withExactHops(12);

        $this->assertSame(12, $r->getExactHops());
    }

    public function testGetMaxHops(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertNull($r->getMaxHops());

        $r->withMaxHops(12);

        $this->assertSame(12, $r->getMaxHops());
    }

    public function testGetMinHops(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertNull($r->getMinHops());

        $r->withMinHops(12);

        $this->assertSame(12, $r->getMinHops());
    }

    public function testGetTypes(): void
    {
        $r = new Relationship(Relationship::DIR_LEFT);
        $this->assertEmpty($r->getTypes());

        $r->withTypes(['a', 'b']);

        $this->assertSame(['a', 'b'], $r->getTypes());
    }

    public function provideVariableLengthRelationshipsWithNameData(): array
    {
        return [
            ['b', 0, 100, Relationship::DIR_UNI, '-[b*0..100]-'],
            ['b', 5, 5, Relationship::DIR_RIGHT, '-[b*5..5]->'],
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
            [[new String_('a')], 10, null, Relationship::DIR_LEFT, "<-[*10.. {`0`: 'a'}]-"],
            [['a' => new String_('b')], null, 10, Relationship::DIR_LEFT, "<-[*..10 {a: 'b'}]-"],
        ];
    }

    public function provideVariableLengthRelationshipsWithNameAndTypeAndPropertiesData(): array
    {
        return [
            ['a', 'a', [], 10, 100, Relationship::DIR_LEFT, "<-[a:a*10..100]-"],
            ['b', 'a', [new String_('a')], null, 10, Relationship::DIR_LEFT, "<-[b:a*..10 {`0`: 'a'}]-"],
            ['a', 'b', [new String_('a')], 10, 100, Relationship::DIR_LEFT, "<-[a:b*10..100 {`0`: 'a'}]-"],
            ['a', '', ['a' => new String_('b')], null, 10, Relationship::DIR_LEFT, "<-[a*..10 {a: 'b'}]-"],
            ['a', ':', ['a' => new String_('b'), new String_('c')], 10, null, Relationship::DIR_LEFT, "<-[a:`:`*10.. {a: 'b', `0`: 'c'}]-"],
        ];
    }

    public function provideWithNameData(): array
    {
        return [
            ['a', Relationship::DIR_UNI, '-[a]-'],
            ['a', Relationship::DIR_LEFT, '<-[a]-'],
            ['a', Relationship::DIR_RIGHT, '-[a]->'],
        ];
    }

    public function provideAddTypeData(): array
    {
        return [
            ['', Relationship::DIR_LEFT, '<--'],
            ['a', Relationship::DIR_LEFT, '<-[:a]-'],
            [':', Relationship::DIR_LEFT, '<-[:`:`]-'],
        ];
    }

    public function provideWithTypesData(): array
    {
        return [
            [['', 'a'], Relationship::DIR_LEFT, '<-[:a]-'],
            [['a', 'b'], Relationship::DIR_LEFT, '<-[:a|b]-'],
            [['', 'a', 'b'], Relationship::DIR_LEFT, '<-[:a|b]-'],
            [['a', '', 'b'], Relationship::DIR_LEFT, '<-[:a|b]-'],
            [['a', 'b', ':'], Relationship::DIR_LEFT, '<-[:a|b|`:`]-'],
        ];
    }

    public function provideWithPropertiesData(): array
    {
        return [
            [[], Relationship::DIR_LEFT, "<--"],
            [[new String_('a')], Relationship::DIR_LEFT, "<-[{`0`: 'a'}]-"],
            [['a' => new String_('b')], Relationship::DIR_LEFT, "<-[{a: 'b'}]-"],
            [['a' => new String_('b'), new String_('c')], Relationship::DIR_LEFT, "<-[{a: 'b', `0`: 'c'}]-"],
            [[':' => new Integer(12)], Relationship::DIR_LEFT, "<-[{`:`: 12}]-"],
            [['a' => 'b', 'c' => 12, 'd' => 12.38], Relationship::DIR_LEFT, "<-[{a: 'b', c: 12, d: 12.38}]-"],
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
            ['b', [new String_('a')], Relationship::DIR_LEFT, "<-[b {`0`: 'a'}]-"],
        ];
    }

    public function provideWithNameAndTypeAndPropertiesData(): array
    {
        return [
            ['a', 'a', [], Relationship::DIR_LEFT, "<-[a:a]-"],
            ['b', 'a', [new String_('a')], Relationship::DIR_LEFT, "<-[b:a {`0`: 'a'}]-"],
            ['a', 'b', [new String_('a')], Relationship::DIR_LEFT, "<-[a:b {`0`: 'a'}]-"],
            ['a', '', ['a' => new String_('b')], Relationship::DIR_LEFT, "<-[a {a: 'b'}]-"],
            ['a', ':', ['a' => new String_('b'), new String_('c')], Relationship::DIR_LEFT, "<-[a:`:` {a: 'b', `0`: 'c'}]-"],
        ];
    }

    public function provideWithMultipleTypesData(): array
    {
        return [
            ['a', [], [], Relationship::DIR_LEFT, "<-[a]-"],
            ['b', ['a'], [new String_('a')], Relationship::DIR_LEFT, "<-[b:a {`0`: 'a'}]-"],
            ['a', ['a', 'b', 'c'], [new String_('a')], Relationship::DIR_LEFT, "<-[a:a|b|c {`0`: 'a'}]-"],
            ['a', ['a', 'b'], [], Relationship::DIR_LEFT, "<-[a:a|b]-"],
        ];
    }
}
