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

use Iterator;
use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\RawClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This class only tests methods of the query class that do not add a neq clause. Use a separate class for testing
 * functions that add new clauses to the query.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryTest extends TestCase
{
    /**
     * @return mixed[][]
     */
    public static function provideLiteralData(): array
    {
        return [
            ['foobar', new String_('foobar')],
            ['0', new String_('0')],
            ['100', new String_('100')],
            [0, new Integer(0)],
            [100, new Integer(100)],
            [10.0, new Float_(10.0)],
            [69420.0, new Float_(69420)],
            [10.0000000000000000000000000000001, new Float_(10.0000000000000000000000000000001)],
            [false, new Boolean(false)],
            [true, new Boolean(true)],
        ];
    }

    public function testNew(): void
    {
        $new = Query::new();

        $this->assertInstanceOf(Query::class, $new);
    }

    public function testIsNotSingleton(): void
    {
        $a = Query::new();
        $b = Query::new();

        $this->assertNotSame($a, $b);
    }

    public function testNodeWithoutLabel(): void
    {
        $actual = Query::node();
        $expected = new Node();

        $this->assertEquals($expected, $actual);
    }

    public function testNodeWithLabel(): void
    {
        $label = "m";

        $actual = Query::node($label);
        $expected = (new Node())->addLabel($label);

        $this->assertEquals($expected, $actual);
    }

    public function testRelationship(): void
    {
        $directions = [Direction::UNI, Direction::LEFT, Direction::RIGHT];

        foreach ($directions as $direction) {
            $expected = new Relationship($direction);
            $actual = Query::relationship($direction);

            $this->assertEquals($expected, $actual);
        }
    }

    public function testVariable(): void
    {
        $variable = Query::variable("foo");
        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertSame("foo", $variable->getName());
    }

    public function testVariableEmpty(): void
    {
        $this->assertInstanceOf(Variable::class, Query::variable());

        $this->assertMatchesRegularExpression('/var[0-9a-f]+/', Query::variable()->toQuery());
    }

    public function testParameter(): void
    {
        $this->assertInstanceOf(Parameter::class, Query::parameter("foo"));
    }

    public function testParameterEmpty(): void
    {
        $this->assertInstanceOf(Parameter::class, Query::parameter());
    }

    /**
     * @dataProvider provideLiteralData
     *
     * @param mixed $literal
     */
    public function testLiteral($literal, PropertyType $expected): void
    {
        $actual = Query::literal($literal);

        $this->assertEquals($expected, $actual);
    }

    public function testBoolean(): void
    {
        $actual = Query::boolean(true);

        $this->assertEquals(new Boolean(true), $actual);
    }

    public function testInteger(): void
    {
        $actual = Query::integer(10);

        $this->assertEquals(new Integer(10), $actual);
    }

    public function testList(): void
    {
        $list = Query::list([]);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testFunction(): void
    {
        $function = Query::function();

        $this->assertSame(Query::procedure(), $function);
    }

    public function testProcedure(): void
    {
        $procedure = Query::procedure();

        $this->assertSame(Procedure::class, $procedure);
    }

    public function testRawExpression(): void
    {
        $rawExpression = Query::rawExpression('UNIMPLEMENTED');

        $this->assertEquals(new RawExpression('UNIMPLEMENTED'), $rawExpression);
    }

    public function testToString(): void
    {
        $query = Query::new()->skip(10);

        $this->assertSame('SKIP 10', $query->__toString());
        $this->assertSame('SKIP 10', (string) $query);
    }

    public function testListOfLiterals(): void
    {
        $list = Query::list(["hello", "world", 1.0, 1, 2, 3, true]);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testListOfMixed(): void
    {
        $list = Query::list([$this->createMock(AnyType::class), "world"]);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testListOfAnyType(): void
    {
        $list = Query::list([
            $this->createMock(AnyType::class),
            $this->createMock(AnyType::class),
        ]);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testNestedList(): void
    {
        $list = Query::list([Query::list([])]);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testIteratorList(): void
    {
        $iterator = new class() implements Iterator
        {
            private int $count = 0;

            public function current(): int
            {
                return 1;
            }

            public function next(): void
            {
                $this->count++;
            }

            public function key(): int
            {
                return 0;
            }

            public function valid(): bool
            {
                // In order to avoid an infinite loop
                return $this->count < 10;
            }

            public function rewind(): void
            {
            }
        };

        $list = Query::list($iterator);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testInvalidList(): void
    {
        $this->expectException(TypeError::class);
        Query::list([new class()
        {
        }, ]);
    }

    public function testMap(): void
    {
        $map = Query::map([]);

        $this->assertInstanceOf(Map::class, $map);
    }

    public function testAddClause(): void
    {
        $clauseMock = new RawClause('FOOBAR', 'foobar');
        $statement = (new Query())->addClause($clauseMock)->build();

        $this->assertSame("FOOBAR foobar", $statement);
    }

    public function testBuild(): void
    {
        $withClause = (new WithClause)->addEntry(new Variable("foobar"));
        $whereClause = (new WhereClause)->addExpression(new Label(new Variable("foo"), "bar"));

        $query = new Query();
        $query->addClause($withClause)->addClause($whereClause);

        $statement = $query->build();

        $this->assertSame("WITH foobar WHERE foo:bar", $statement);

        $variableMock = new Variable("a");
        $nodeMock = (new Node)->withVariable($variableMock);

        $pathMock = new Path([$nodeMock, (new Node)->withVariable('b')], [new Relationship(Direction::RIGHT)]);
        $numeralMock = new Integer(12);
        $booleanMock = new GreaterThan($variableMock, new Variable('b'), false);
        $propertyMock = new Property($variableMock, 'b');

        $query = new Query();
        $statement = $query->match([$pathMock, $nodeMock])
            ->returning(["#" => $nodeMock], true)
            ->create([$pathMock, $nodeMock])
            ->create($pathMock)
            ->delete([$variableMock, $variableMock])
            ->detachDelete([$variableMock, $variableMock])
            ->limit($numeralMock)
            ->merge($nodeMock)
            ->optionalMatch([$nodeMock, $nodeMock])
            ->orderBy([$propertyMock, $propertyMock], true)
            ->remove($propertyMock)
            ->set([])
            ->where($booleanMock)
            ->with(["#" => $nodeMock])
            ->build();

        $this->assertSame("MATCH (a)-->(b), (a) RETURN DISTINCT a AS `#` CREATE (a)-->(b), (a) CREATE (a)-->(b) DELETE a, a DETACH DELETE a, a LIMIT 12 MERGE (a) OPTIONAL MATCH (a), (a) ORDER BY a.b, a.b DESCENDING REMOVE a.b WHERE a > b WITH a AS `#`", $statement);
    }

    public function testBuildEmpty(): void
    {
        $query = new Query();

        $this->assertSame("", $query->build());
    }

    public function testInt(): void
    {
        $literal = Query::literal(1);
        $this->assertInstanceOf(Integer::class, $literal);
        $this->assertEquals('1', $literal->toQuery());
    }

    public function testFloatLiteral(): void
    {
        $literal = Query::literal(1.2);
        $this->assertInstanceOf(Float_::class, $literal);
        $this->assertEquals('1.2', $literal->toQuery());
    }

    public function testFloat(): void
    {
        $float = Query::float(1.0);

        $this->assertEquals(new Float_(1.0), $float);
    }

    public function testStringLiteral(): void
    {
        $literal = Query::literal('abc');
        $this->assertInstanceOf(String_::class, $literal);
        $this->assertEquals("'abc'", $literal->toQuery());
    }

    public function testString(): void
    {
        $string = Query::string('foo');

        $this->assertEquals(new String_('foo'), $string);
    }

    public function testStringAble(): void
    {
        // @phpstan-ignore-next-line
        $literal = Query::literal(new class()
        {
            public function __toString(): string
            {
                return 'stringable abc';
            }
        });
        $this->assertInstanceOf(String_::class, $literal);
        $this->assertEquals("'stringable abc'", $literal->toQuery());
    }

    public function testBool(): void
    {
        $literal = Query::literal(true);
        $this->assertInstanceOf(Boolean::class, $literal);
        $this->assertEquals("true", $literal->toQuery());
    }

    public function testInvalidLiteral(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::literal(Query::literal(true));
    }

    public function testLiteralReference(): void
    {
        $value = Query::literal();

        $this->assertSame(Literal::class, $value);
    }

    public function testCanConstruct(): void
    {
        $query = new Query();

        $this->assertInstanceOf(Query::class, $query);
    }

    public function testAutomaticIdentifierGeneration(): void
    {
        $node = Query::node();

        $this->assertMatchesRegularExpression('/var[0-9a-f]+\.foo/', $node->property('foo')->toQuery());

        $node->withVariable('foo');

        $this->assertSame('foo.bar', $node->property('bar')->toQuery());

        $node = Query::node();
        $statement = Query::new()->match($node)->returning($node)->build();

        $this->assertMatchesRegularExpression('/MATCH \(var[0-9a-f]+\) RETURN var[0-9a-f]+/', $statement);

        $node = Query::node();

        $this->assertInstanceOf(Variable::class, $node->getVariable());
    }
}
