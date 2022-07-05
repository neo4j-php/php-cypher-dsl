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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Patterns\Node
 */
class NodeTest extends TestCase
{
    public function testEmptyNode(): void
    {
        $node = new Node();

        $this->assertSame("()", $node->toQuery());

        $this->assertNull($node->getProperties());
        $this->assertEquals([], $node->getLabels());
        $this->assertFalse($node->hasVariable());
    }

    public function testBacktickThrowsException(): void
    {
        $node = new Node();

        $this->expectException(InvalidArgumentException::class);
        $this->expectDeprecationMessage('A name can only contain alphanumeric characters and underscores');
        $node->setVariable('abcdr`eer');
    }

    /**
     * @dataProvider provideOnlyLabelData
     * @param string $label
     * @param string $expected
     */
    public function testOnlyLabel(string $label, string $expected): void
    {
        $node = new Node();
        $node->labeled($label);

        $this->assertSame($expected, $node->toQuery());

        $this->assertNull($node->getProperties());
        $this->assertEquals([$label], $node->getLabels());
        $this->assertFalse($node->hasVariable());
    }

    /**
     * @dataProvider provideOnlyNameData
     * @param string $name
     * @param string $expected
     */
    public function testOnlyName(string $name, string $expected): void
    {
        $node = new Node();
        $node->setVariable($name);

        $this->assertSame($expected, $node->toQuery());

        $this->assertNull($node->getProperties());
        $this->assertEquals([], $node->getLabels());
        $this->assertTrue($node->hasVariable());

        $this->assertEquals($name, $node->getVariable()->getName());
    }

    /**
     * @dataProvider provideOnlyPropertiesData
     * @param array $properties
     * @param string $expected
     */
    public function testOnlyProperties(array $properties, string $expected): void
    {
        $node = new Node();
        $node->addProperties($properties);

        $this->assertSame($expected, $node->toQuery());

        $this->assertEquals(new PropertyMap($properties), $node->getProperties());
        $this->assertEquals([], $node->getLabels());
        $this->assertFalse($node->hasVariable());
    }

    /**
     * @dataProvider provideWithNameAndLabelData
     * @param string $name
     * @param string $label
     * @param string $expected
     */
    public function testWithNameAndLabel(string $name, string $label, string $expected): void
    {
        $node = new Node();
        $node->labeled($label)->setVariable($name);

        $this->assertSame($expected, $node->toQuery());

        $this->assertNull($node->getProperties());
        $this->assertEquals([$label], $node->getLabels());
        $this->assertTrue($node->hasVariable());

        $this->assertEquals($name, $node->getVariable()->getName());
    }

    /**
     * @dataProvider provideWithNameAndPropertiesData
     * @param string $name
     * @param array $properties
     * @param string $expected
     */
    public function testWithNameAndProperties(string $name, array $properties, string $expected): void
    {
        $node = new Node();
        $node->setVariable($name)->addProperties($properties);

        $this->assertSame($expected, $node->toQuery());

        $this->assertEquals(new PropertyMap($properties), $node->getProperties());
        $this->assertEquals([], $node->getLabels());
        $this->assertTrue($node->hasVariable());

        $this->assertEquals($name, $node->getVariable()->getName());
    }

    /**
     * @dataProvider provideWithLabelAndPropertiesData
     * @param string $label
     * @param array $properties
     * @param string $expected
     */
    public function testWithLabelAndProperties(string $label, array $properties, string $expected): void
    {
        $node = new Node();
        $node->labeled($label)->addProperties($properties);

        $this->assertSame($expected, $node->toQuery());

        $this->assertEquals(new PropertyMap($properties), $node->getProperties());
        $this->assertEquals([$label], $node->getLabels());
        $this->assertFalse($node->hasVariable());
    }

    /**
     * @dataProvider provideWithNameAndLabelAndPropertiesData
     * @param string $name
     * @param string $label
     * @param array $properties
     * @param string $expected
     */
    public function testWithNameAndLabelAndProperties(string $name, string $label, array $properties, string $expected): void
    {
        $node = new Node();
        $node->setVariable($name)->labeled($label)->addProperties($properties);

        $this->assertSame($expected, $node->toQuery());

        $this->assertEquals(new PropertyMap($properties), $node->getProperties());
        $this->assertEquals([$label], $node->getLabels());
        $this->assertTrue($node->hasVariable());

        $this->assertEquals($name, $node->getVariable()->getName());
    }

    /**
     * @dataProvider provideMultipleLabelsData
     * @param array $labels
     * @param string $expected
     */
    public function testMultipleLabels(array $labels, string $expected): void
    {
        $node = new Node();

        foreach ($labels as $label) {
            $node->labeled($label);
        }

        $this->assertSame($expected, $node->toQuery());

        $this->assertNull($node->getProperties());
        $this->assertEquals($labels, $node->getLabels());
        $this->assertFalse($node->hasVariable());
    }

    public function testSetterSameAsConstructor(): void
    {
        $label = "__test__";
        $viaConstructor = new Node($label);
        $viaSetter = (new Node())->labeled($label);

        $this->assertSame($viaConstructor->toQuery(), $viaSetter->toQuery(), "Setting label via setter has different effect than using constructor");
    }

    public function testAddingProperties(): void
    {
        $node = new Node();

        $node->addProperty("foo", new StringLiteral("bar"));

        $this->assertSame("({foo: 'bar'})", $node->toQuery());

        $node->addProperty("foo", new StringLiteral("bar"));

        $this->assertSame("({foo: 'bar'})", $node->toQuery());

        $node->addProperty("baz", new StringLiteral("bar"));

        $this->assertSame("({foo: 'bar', baz: 'bar'})", $node->toQuery());

        $node->addProperties(["foo" => new StringLiteral("baz"), "qux" => new StringLiteral("baz")]);

        $this->assertSame("({foo: 'baz', baz: 'bar', qux: 'baz'})", $node->toQuery());
    }

    public function testPropertyWithName(): void
    {
        $node = new Node();
        $node->setVariable('example');

        $this->assertSame('example.foo', $node->property('foo')->toQuery());
    }

    public function testPropertyWithoutName(): void
    {
        $node = new Node();

        $this->assertMatchesRegularExpression("/^var[0-9a-f]+\.foo$/", $node->property('foo')->toQuery());
    }

    public function provideOnlyLabelData(): array
    {
        return [
            ['a', '(:a)'],
            ['A', '(:A)'],
            [':', '(:`:`)'],
        ];
    }

    public function provideOnlyNameData(): array
    {
        return [
            ['a', '(a)'],
            ['A', '(A)'],
        ];
    }

    public function provideWithNameAndLabelData(): array
    {
        return [
            ['a', 'a', '(a:a)'],
            ['A', ':', '(A:`:`)'],
        ];
    }

    public function provideWithNameAndPropertiesData(): array
    {
        return [
            ['a', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(a {a: 'b', b: 'c'})"],
            ['b', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(b {a: 0, b: 1})"],
            ['c', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(c {`:`: [1, 'a']})"],
        ];
    }

    public function provideWithLabelAndPropertiesData(): array
    {
        return [
            ['a', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(:a {a: 'b', b: 'c'})"],
            ['b', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(:b {a: 0, b: 1})"],
            ['c', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(:c {`:`: [1, 'a']})"],
        ];
    }

    public function provideOnlyPropertiesData(): array
    {
        return [
            [['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "({a: 'b', b: 'c'})"],
            [['a' => new Decimal(0), 'b' => new Decimal(1)], "({a: 0, b: 1})"],
            [[':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "({`:`: [1, 'a']})"],
        ];
    }

    public function provideWithNameAndLabelAndPropertiesData(): array
    {
        return [
            ['a', 'd', ['a' => new StringLiteral('b'), 'b' => new StringLiteral('c')], "(a:d {a: 'b', b: 'c'})"],
            ['b', 'e', ['a' => new Decimal(0), 'b' => new Decimal(1)], "(b:e {a: 0, b: 1})"],
            ['c', 'f', [':' => new ExpressionList([new Decimal(1), new StringLiteral('a')])], "(c:f {`:`: [1, 'a']})"],
        ];
    }

    public function provideMultipleLabelsData(): array
    {
        return [
            [['a'], '(:a)'],
            [['A'], '(:A)'],
            [[':'], '(:`:`)'],
            [['a', 'b'], '(:a:b)'],
            [['A', 'B'], '(:A:B)'],
            [[':', 'a'], '(:`:`:a)'],
        ];
    }
}