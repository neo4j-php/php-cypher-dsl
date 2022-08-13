<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use function WikibaseSolutions\CypherDSL\float;
use function WikibaseSolutions\CypherDSL\function_;
use function WikibaseSolutions\CypherDSL\integer;
use function WikibaseSolutions\CypherDSL\list_;
use function WikibaseSolutions\CypherDSL\literal;
use function WikibaseSolutions\CypherDSL\map;
use function WikibaseSolutions\CypherDSL\node;
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
class FunctionsTest extends TestCase
{
    public function testQueryReturnsQuery()
    {
        $query = query();

        $this->assertInstanceOf(Query::class, $query);
    }

    public function testNodeReturnsNode()
    {
        $node = node('label');

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('(:label)', $node->toQuery());
    }

    public function testNodeAcceptsEmptyLabel()
    {
        $node = node();

        $this->assertInstanceOf(Node::class, $node);
        $this->assertSame('()', $node->toQuery());
    }

    public function testNodeOnlyAcceptsStringLabel()
    {
        $this->expectException(TypeError::class);

        node([]);
    }

    public function testRelationshipReturnsRelationship()
    {
        $relationship = relationship(Relationship::DIR_RIGHT);

        $this->assertInstanceOf(Relationship::class, $relationship);
        $this->assertSame('-->', $relationship->toQuery());
    }

    public function testVariableReturnsVariable()
    {
        $variable = variable('foobar');

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertSame('foobar', $variable->toQuery());
    }

    public function testVariableAcceptsEmptyArgument()
    {
        $variable = variable();

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertStringMatchesFormat('var%s', $variable->toQuery());
    }

    public function testVariableOnlyAcceptsString()
    {
        $this->expectException(TypeError::class);

        variable([]);
    }

    public function testLiteralReturnsLiteralClass()
    {
        $literal = literal();

        $this->assertSame(Literal::class, $literal);
    }

    public function testLiteralReturnsLiteral()
    {
        $literal = literal('foobar');

        $this->assertInstanceOf(String_::class, $literal);
        $this->assertSame("'foobar'", $literal->toQuery());

        $literal = literal(true);

        $this->assertInstanceOf(Boolean::class, $literal);
        $this->assertSame("true", $literal->toQuery());
    }

    public function testBooleanReturnsBoolean()
    {
        $boolean = \WikibaseSolutions\CypherDSL\boolean(true);

        $this->assertInstanceOf(Boolean::class, $boolean);
        $this->assertSame('true', $boolean->toQuery());
    }

    public function testBooleanOnlyAcceptsBoolean()
    {
        $this->expectException(TypeError::class);

        \WikibaseSolutions\CypherDSL\boolean([]);
    }

    public function testStringReturnsString()
    {
        $string = string('hello world');

        $this->assertInstanceOf(String_::class, $string);
        $this->assertSame("'hello world'", $string->toQuery());
    }

    public function testStringOnlyAcceptsString()
    {
        $this->expectException(TypeError::class);

        string([]);
    }

	public function testIntegerReturnsInteger()
	{
		$integer = integer(1);

		$this->assertInstanceOf(Integer::class, $integer);
		$this->assertSame("1", $integer->toQuery());
	}

	public function testIntegerOnlyAcceptsInteger()
	{
		$this->expectException(TypeError::class);

		integer([]);
	}

	public function testFloatReturnsFloat()
	{
		$float = float(1.1);

		$this->assertInstanceOf(Float_::class, $float);
		$this->assertSame("1.1", $float->toQuery());
	}

	public function testFloatOnlyAcceptsFloat()
	{
		$this->expectException(TypeError::class);

		float([]);
	}

	public function testListReturnsList()
	{
		$list = list_(['a', 'b', 'c']);

		$this->assertInstanceOf(List_::class, $list);
		$this->assertSame("['a', 'b', 'c']", $list->toQuery());
	}

	public function testListOnlyAcceptsArray()
	{
		$this->expectException(TypeError::class);

		list_(1);
	}

	public function testMapReturnsMap()
	{
		$map = map(['a' => 'b', 'c' => 'd']);

		$this->assertInstanceOf(Map::class, $map);
		$this->assertSame("{a: 'b', c: 'd'}", $map->toQuery());
	}

	public function testMapOnlyAcceptsArray()
	{
		$this->expectException(TypeError::class);

		map(1);
	}

	public function testFunctionReturnsFuncClass()
	{
		$function = function_();

		$this->assertSame(Procedure::class, $function);
	}

	public function testRawReturnsRawExpression()
	{
		$raw = raw('(unimplemented feature)');

		$this->assertInstanceOf(RawExpression::class, $raw);
		$this->assertSame('(unimplemented feature)', $raw->toQuery());
	}

	public function testRawOnlyAcceptsString()
	{
		$this->expectException(TypeError::class);

		raw([]);
	}
}
