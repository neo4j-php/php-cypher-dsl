<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;

/**
 * @covers \WikibaseSolutions\CypherDSL\Patterns\Node
 */
class NodeTest extends TestCase
{
    public function testEmptyNode(): void
    {
        $node = new Node();

        $this->assertSame("()", $node->toQuery());
    }

    public function testBacktickIsEscaped(): void
    {
        $node = new Node();

        $node->withVariable('abcdr`eer');
        $this->assertEquals('(`abcdr``eer`)', $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyLabelData
     * @param string $label
     * @param string $expected
     */
    public function testOnlyLabel(string $label, string $expected): void
    {
        $node = new Node();
        $node->addLabel($label);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyNameData
     * @param string $name
     * @param string $expected
     */
    public function testOnlyName(string $name, string $expected): void
    {
        $node = new Node();
        $node->withVariable($name);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideOnlyPropertiesData
     * @param array $properties
     * @param string $expected
     */
    public function testOnlyProperties(array $properties, string $expected): void
    {
        $node = new Node();
        $node->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
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
        $node->addLabel($label)->withVariable($name);

        $this->assertSame($expected, $node->toQuery());
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
        $node->withVariable($name)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
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
        $node->addLabel($label)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
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
        $node->withVariable($name)->addLabel($label)->withProperties($properties);

        $this->assertSame($expected, $node->toQuery());
    }

    /**
     * @dataProvider provideMultipleLabelsData
     * @param array $labels
     * @param string $expected
     */
    public function testMultipleLabels(array $labels, string $expected): void
    {
        $node = new Node();
        $node->withLabels($labels);

        $this->assertSame($expected, $node->toQuery());
    }

    public function testSetterSameAsConstructor(): void
    {
        $label = "__test__";
        $viaConstructor = new Node($label);
        $viaSetter = (new Node())->addLabel($label);

        $this->assertSame($viaConstructor->toQuery(), $viaSetter->toQuery(), "Setting label via setter has different effect than using constructor");
    }

    public function testAddingProperties(): void
    {
        $node = new Node();

        $node->addProperty("foo", new String_("bar"));
        $this->assertSame("({foo: 'bar'})", $node->toQuery());

        $node->addProperty("foo", new String_("bar"));
        $this->assertSame("({foo: 'bar'})", $node->toQuery());

        $node->addProperty("baz", new String_("bar"));
        $this->assertSame("({foo: 'bar', baz: 'bar'})", $node->toQuery());

        $node->addProperty("baz", "test");
        $this->assertSame("({foo: 'bar', baz: 'test'})", $node->toQuery());

        $node->addProperties(["foo" => new String_("baz"), "qux" => new String_("baz")]);
        $this->assertSame("({foo: 'baz', baz: 'test', qux: 'baz'})", $node->toQuery());

        $node->addProperties(new Map(["foo" => new String_("test")]));
        $this->assertSame("({foo: 'test', baz: 'test', qux: 'baz'})", $node->toQuery());
    }

    public function testAddingPropertiesToVariableFails(): void
    {
        $node = new Node();

        $node->withProperties(new Variable());
        $this->expectException(TypeError::class);
        $node->addProperty('foo', 'bar');
    }

    public function testGetLabels(): void
    {
        $labels = ["hello", "world"];

        $node = new Node();
        $node->withLabels($labels);

        $this->assertSame($labels, $node->getLabels());
    }

    public function testGetProperties(): void
    {
        $properties = new Map(['foo' => new String_('bar')]);

        $node = new Node();
        $node->withProperties($properties);

        $this->assertSame($properties, $node->getProperties());
    }

    public function testRelationship(): void
    {
        $node = new Node("City");
        $relationship = new Relationship(Relationship::DIR_RIGHT);
        $relationship->addType("LIVES_IN");

        $amsterdam = new Node("City");
        $amsterdam->withProperties(["city" => "Amsterdam"]);

        $this->assertSame("(:City)-[:LIVES_IN]->(:City {city: 'Amsterdam'})", $node->relationship($relationship, $amsterdam)->toQuery());
    }

    public function testRelationshipTo(): void
    {
        $node = new Node("City");
        $amsterdam = new Node("City");
        $amsterdam->withProperties(["city" => "Amsterdam"]);

        $relationship = $node->relationshipTo($amsterdam, "LIVES_IN");

        $this->assertSame("(:City)-[:LIVES_IN]->(:City {city: 'Amsterdam'})", $relationship->toQuery());
    }

    public function testRelationshipFrom(): void
    {
        $node = new Node("City");
        $amsterdam = new Node("City");
        $amsterdam->withProperties(["city" => "Amsterdam"]);

        $relationship = $node->relationshipFrom($amsterdam, "LIVES_IN");

        $this->assertSame("(:City)<-[:LIVES_IN]-(:City {city: 'Amsterdam'})", $relationship->toQuery());
    }

    public function testRelationshipUni(): void
    {
        $node = new Node("City");
        $amsterdam = new Node("City");
        $amsterdam->withProperties(["city" => "Amsterdam"]);

        $relationship = $node->relationshipUni($amsterdam, "LIVES_IN");

        $this->assertSame("(:City)-[:LIVES_IN]-(:City {city: 'Amsterdam'})", $relationship->toQuery());
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
            ['a', ['a' => new String_('b'), 'b' => new String_('c')], "(a {a: 'b', b: 'c'})"],
            ['b', ['a' => new Integer(0), 'b' => new Float_(1)], "(b {a: 0, b: 1.0})"],
            ['c', [':' => new List_([new Integer(1), new String_('a')])], "(c {`:`: [1, 'a']})"],
        ];
    }

    public function provideWithLabelAndPropertiesData(): array
    {
        return [
            ['a', ['a' => new String_('b'), 'b' => new String_('c')], "(:a {a: 'b', b: 'c'})"],
            ['b', ['a' => new Integer(0), 'b' => new Float_(1)], "(:b {a: 0, b: 1.0})"],
            ['c', [':' => new List_([new Integer(1), new String_('a')])], "(:c {`:`: [1, 'a']})"],
        ];
    }

    public function provideOnlyPropertiesData(): array
    {
        return [
            [['a' => new String_('b'), 'b' => new String_('c')], "({a: 'b', b: 'c'})"],
            [['a' => new Integer(0), 'b' => new Float_(-1.0)], "({a: 0, b: -1.0})"],
            [[':' => new List_([new Integer(1), new String_('a')])], "({`:`: [1, 'a']})"],
        ];
    }

    public function provideWithNameAndLabelAndPropertiesData(): array
    {
        return [
            ['a', 'd', ['a' => new String_('b'), 'b' => new String_('c')], "(a:d {a: 'b', b: 'c'})"],
            ['b', 'e', ['a' => new Integer(0), 'b' => new Float_(1)], "(b:e {a: 0, b: 1.0})"],
            ['c', 'f', [':' => new List_([new Integer(1), new String_('a')])], "(c:f {`:`: [1, 'a']})"],
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
