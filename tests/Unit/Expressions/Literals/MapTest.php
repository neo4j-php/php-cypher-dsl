<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Map
 */
final class MapTest extends TestCase
{
    public function testEmpty(): void
    {
        $map = new Map([]);

        $this->assertSame("{}", $map->toQuery());
    }

    /**
     * @dataProvider provideNumericalKeysData
     */
    public function testNumericalKeys(array $properties, string $expected): void
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    /**
     * @dataProvider provideStringKeysData
     */
    public function testStringKeys(array $properties, string $expected): void
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    /**
     * @dataProvider provideNestedMapsData
     */
    public function testNestedMaps(array $properties, string $expected): void
    {
        $map = new Map($properties);

        $this->assertSame($expected, $map->toQuery());
    }

    public function testMergeWith(): void
    {
        $map = new Map(["foo" => new String_("bar")]);
        $map->mergeWith(new Map(["boo" => new String_("far")]));

        $this->assertSame("{foo: 'bar', boo: 'far'}", $map->toQuery());

        $map->mergeWith($map);

        $this->assertSame("{foo: 'bar', boo: 'far'}", $map->toQuery());
    }

    public function testAdd(): void
    {
        $map = new Map(["foo" => new String_("bar")]);
        $map->add('foo', new String_("baz"));

        $this->assertSame("{foo: 'baz'}", $map->toQuery());

        $map->add('boo', new String_("far"));

        $this->assertSame("{foo: 'baz', boo: 'far'}", $map->toQuery());

        $map->add('boo', false);

        $this->assertSame("{foo: 'baz', boo: false}", $map->toQuery());
    }

    public function testIsEmpty(): void
    {
        $map = new Map();

        $this->assertTrue($map->isEmpty());

        $map->add('boo', 'far');

        $this->assertFalse($map->isEmpty());
    }

    public function testIsInstanceOfMapType(): void
    {
        $map = new Map();

        $this->assertInstanceOf(MapType::class, $map);
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
