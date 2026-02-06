<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use function WikibaseSolutions\CypherDSL\float;
use function WikibaseSolutions\CypherDSL\function_;
use function WikibaseSolutions\CypherDSL\integer;
use function WikibaseSolutions\CypherDSL\list_;
use function WikibaseSolutions\CypherDSL\literal;
use function WikibaseSolutions\CypherDSL\map;
use function WikibaseSolutions\CypherDSL\node;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\raw;
use function WikibaseSolutions\CypherDSL\relationship;
use function WikibaseSolutions\CypherDSL\string;
use function WikibaseSolutions\CypherDSL\variable;

/**
 * Tests the functions in "functions.php".
 *
 * @coversNothing
 */
final class FunctionsTest extends TestCase
{
    public function testQueryReturnsQuery(): void
    {
        $query = query();

        $this->assertInstanceOf(Query::class, $query);
    }

    public function testNodeReturnsNode(): void
    {
        $node = node('label');

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('(:label)', $node->toQuery());
    }

    public function testNodeAcceptsEmptyLabel(): void
    {
        $node = node();

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('()', $node->toQuery());
    }

    public function testNodeOnlyAcceptsStringLabel(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        node([]);
    }

    public function testRelationshipReturnsRelationship(): void
    {
        $relationship = relationship(Direction::RIGHT);

        $this->assertInstanceOf(Relationship::class, $relationship);
        $this->assertSame('-->', $relationship->toQuery());
    }

    public function testVariableReturnsVariable(): void
    {
        $variable = variable('foobar');

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertSame('foobar', $variable->toQuery());
    }

    public function testVariableAcceptsEmptyArgument(): void
    {
        $variable = variable();

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertStringMatchesFormat('var%s', $variable->toQuery());
    }

    public function testVariableOnlyAcceptsString(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        variable([]);
    }

    public function testLiteralReturnsLiteralClass(): void
    {
        $literal = literal();

        $this->assertSame(Literal::class, $literal);
    }

    public function testLiteralReturnsLiteral(): void
    {
        $literal = literal('foobar');

        $this->assertInstanceOf(String_::class, $literal);
        $this->assertSame("'foobar'", $literal->toQuery());

        $literal = literal(true);

        $this->assertInstanceOf(Boolean::class, $literal);
        $this->assertSame("true", $literal->toQuery());
    }

    public function testBooleanReturnsBoolean(): void
    {
        $boolean = \WikibaseSolutions\CypherDSL\boolean(true);

        $this->assertInstanceOf(Boolean::class, $boolean);
        $this->assertSame('true', $boolean->toQuery());
    }

    public function testBooleanOnlyAcceptsBoolean(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        \WikibaseSolutions\CypherDSL\boolean([]);
    }

    public function testStringReturnsString(): void
    {
        $string = string('hello world');

        $this->assertInstanceOf(String_::class, $string);
        $this->assertSame("'hello world'", $string->toQuery());
    }

    public function testStringOnlyAcceptsString(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        string([]);
    }

    public function testIntegerReturnsInteger(): void
    {
        $integer = integer(1);

        $this->assertInstanceOf(Integer::class, $integer);
        $this->assertSame("1", $integer->toQuery());
    }

    public function testIntegerOnlyAcceptsInteger(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        integer([]);
    }

    public function testFloatReturnsFloat(): void
    {
        $float = float(1.1);

        $this->assertInstanceOf(Float_::class, $float);
        $this->assertSame("1.1", $float->toQuery());
    }

    public function testFloatOnlyAcceptsFloat(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        float([]);
    }

    public function testListReturnsList(): void
    {
        $list = list_(['a', 'b', 'c']);

        $this->assertInstanceOf(List_::class, $list);
        $this->assertSame("['a', 'b', 'c']", $list->toQuery());
    }

    public function testListOnlyAcceptsArray(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        list_(1);
    }

    public function testMapReturnsMap(): void
    {
        $map = map(['a' => 'b', 'c' => 'd']);

        $this->assertInstanceOf(Map::class, $map);
        $this->assertSame("{a: 'b', c: 'd'}", $map->toQuery());
    }

    public function testMapOnlyAcceptsArray(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        map(1);
    }

    public function testFunctionReturnsFuncClass(): void
    {
        $function = function_();

        $this->assertSame(Procedure::class, $function);
    }

    public function testRawReturnsRawExpression(): void
    {
        $raw = raw('(unimplemented feature)');

        $this->assertInstanceOf(RawExpression::class, $raw);
        $this->assertSame('(unimplemented feature)', $raw->toQuery());
    }

    public function testRawOnlyAcceptsString(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        raw([]);
    }
}
