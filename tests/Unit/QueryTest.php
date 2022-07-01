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

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Assignment;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Exists;
use WikibaseSolutions\CypherDSL\ExpressionList;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Parameter;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryTest extends TestCase
{
    use TestHelper;

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
        $expected = (new Node())->labeled($label);

        $this->assertEquals($expected, $actual);
    }

    public function testRelationship(): void
    {
        $directions = [Relationship::DIR_UNI, Relationship::DIR_LEFT, Relationship::DIR_RIGHT];

        foreach ($directions as $direction) {
            $expected = new Relationship($direction);
            $actual = Query::relationship($direction);

            $this->assertEquals($expected, $actual);
        }
    }

    public function testVariable(): void
    {
        $this->assertInstanceOf(Variable::class, Query::variable("foo"));
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

    /**
     * @dataProvider provideLiteralData
     * @param        $literal
     * @param PropertyType $expected
     */
    public function testLiteral($literal, PropertyType $expected): void
    {
        $actual = Query::literal($literal);

        $this->assertEquals($expected, $actual);
    }

    public function testList(): void
    {
        $list = Query::list([]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfLiterals(): void
    {
        $list = Query::list(["hello", "world", 1.0, 1, 2, 3, true]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfMixed(): void
    {
        $list = Query::list([$this->getQueryConvertibleMock(AnyType::class, "hello"), "world"]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfAnyType(): void
    {
        $list = Query::list([
            $this->getQueryConvertibleMock(AnyType::class, "hello"),
            $this->getQueryConvertibleMock(AnyType::class, "world"),
        ]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testNestedList(): void
    {
        $list = Query::list([Query::list([])]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testIteratorList(): void
    {
        $iterator = new class () implements \Iterator {
            private int $count = 0;

            public function current()
            {
                return 1;
            }

            public function next()
            {
                $this->count++;

                return 1;
            }

            public function key()
            {
                return 0;
            }

            public function valid()
            {
                // In order to avoid an infinite loop
                return $this->count < 10;
            }

            public function rewind()
            {
            }
        };

        $list = Query::list($iterator);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testInvalidList(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Query::list([new class () {}]);
    }

    public function testMap(): void
    {
        $map = Query::map([]);

        $this->assertInstanceOf(PropertyMap::class, $map);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFunction(): void
    {
        Query::function()::raw("test", []);
    }

    public function testMatch(): void
    {
        $m = $this->getQueryConvertibleMock(NodeType::class, "(m:Movie)");

        $statement = (new Query())->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);

        $statement = (new Query())->match([$m, $m])->build();

        $this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMatchTypeAcceptance(): void
    {
        $path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
        $node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

        (new Query())->match([$path, $node]);
    }

    public function testMatchRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->match($m);
    }

    public function testReturning(): void
    {
        $m = $this->getQueryConvertibleMock(StructuralType::class, "(m:Movie)");

        $statement = (new Query())->returning($m)->build();

        $this->assertSame("RETURN (m:Movie)", $statement);

        $statement = (new Query())->returning(["n" => $m])->build();

        $this->assertSame("RETURN (m:Movie) AS n", $statement);
    }

    public function testReturningRejectsNotAnyType(): void
    {
        $m = new class () {};

        $this->expectException(TypeError::class);

        (new Query())->returning([$m]);
    }

    public function testReturningWithNode(): void
    {
        $node = Query::node("m");

        $statement = (new Query())->returning($node)->build();

        $this->assertMatchesRegularExpression("/(RETURN var[0-9a-f]+)/", $statement);

        $node = Query::node("m");
        $node->setVariable('example');

        $statement = (new Query())->returning($node)->build();

        $this->assertSame('RETURN example', $statement);
    }

    public function testCreate(): void
    {
        $m = $this->getQueryConvertibleMock(PathType::class, "(m:Movie)-[:RELATED]->(b)");

        $statement = (new Query())->create($m)->build();

        $this->assertSame("CREATE (m:Movie)-[:RELATED]->(b)", $statement);

        $statement = (new Query())->create([$m, $m])->build();

        $this->assertSame("CREATE (m:Movie)-[:RELATED]->(b), (m:Movie)-[:RELATED]->(b)", $statement);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCreateTypeAcceptance(): void
    {
        $path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
        $node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

        (new Query())->create([$path, $node]);
    }

    public function testCreateRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->create([$m, $m]);
    }

    public function testDelete(): void
    {
        $m = $this->getQueryConvertibleMock(Variable::class, "m");

        $statement = (new Query())->delete($m)->build();

        $this->assertSame("DELETE m", $statement);

        $statement = (new Query())->delete([$m, $m])->build();

        $this->assertSame("DELETE m, m", $statement);
    }

    public function testDeleteRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->delete([$m, $m]);
    }

    public function testDetachDelete(): void
    {
        $m = $this->getQueryConvertibleMock(Variable::class, "m");

        $statement = (new Query())->detachDelete($m)->build();

        $this->assertSame("DETACH DELETE m", $statement);

        $statement = (new Query())->detachDelete([$m, $m])->build();

        $this->assertSame("DETACH DELETE m, m", $statement);
    }

    public function testDetachDeleteRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->detachDelete([$m, $m]);
    }

    public function testLimit(): void
    {
        $expression = $this->getQueryConvertibleMock(NumeralType::class, "12");

        $statement = (new Query())->limit($expression)->build();

        $this->assertSame("LIMIT 12", $statement);
    }

    public function testMerge(): void
    {
        $pattern = $this->getQueryConvertibleMock(PathType::class, "(m)->(b)");

        $statement = (new Query())->merge($pattern)->build();

        $this->assertSame("MERGE (m)->(b)", $statement);

        $onCreate = $this->getQueryConvertibleMock(Clause::class, "DELETE (m:Movie)");
        $onMatch = $this->getQueryConvertibleMock(Clause::class, "CREATE (m:Movie)");

        $statement = (new Query())->merge($pattern, $onCreate, $onMatch)->build();

        $this->assertSame("MERGE (m)->(b) ON CREATE DELETE (m:Movie) ON MATCH CREATE (m:Movie)", $statement);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testMergeTypeAcceptance(): void
    {
        $path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
        $node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

        (new Query())->merge($path);
        (new Query())->merge($node);
    }

    public function testMergeRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->optionalMatch([$m, $m]);
    }

    public function testOptionalMatch(): void
    {
        $pattern = $this->getQueryConvertibleMock(NodeType::class, "(m)");

        $statement = (new Query())->optionalMatch($pattern)->build();

        $this->assertSame("OPTIONAL MATCH (m)", $statement);

        $statement = (new Query())->optionalMatch([$pattern, $pattern])->build();

        $this->assertSame("OPTIONAL MATCH (m), (m)", $statement);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testOptionalMatchTypeAcceptance(): void
    {
        $path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
        $node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

        (new Query())->optionalMatch([$path, $node]);
    }

    public function testOptionalMatchRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->optionalMatch([$m, $m]);
    }

    public function testOrderBy(): void
    {
        $property = $this->getQueryConvertibleMock(Property::class, "a.foo");

        $statement = (new Query())->orderBy($property)->build();

        $this->assertSame("ORDER BY a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property])->build();

        $this->assertSame("ORDER BY a.foo, a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property], false)->build();

        $this->assertSame("ORDER BY a.foo, a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property], true)->build();

        $this->assertSame("ORDER BY a.foo, a.foo DESCENDING", $statement);
    }

    public function testOrderByRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->orderBy([$m, $m]);
    }

    public function testRemove(): void
    {
        $expression = $this->getQueryConvertibleMock(Property::class, "a.age");

        $statement = (new Query())->remove($expression)->build();

        $this->assertSame("REMOVE a.age", $statement);
    }

    public function testRemoveRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->remove($m);
    }

    public function testSet(): void
    {
        $expression = $this->getQueryConvertibleMock(Assignment::class, "a.age");

        $statement = (new Query())->set($expression)->build();

        $this->assertSame("SET a.age", $statement);

        $statement = (new Query())->set([$expression, $expression])->build();

        $this->assertSame("SET a.age, a.age", $statement);
    }

    public function testSetRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->set([$m, $m]);
    }

    public function testSetWithLabel(): void
    {
        $label = Query::variable("n")->labeled(["LABEL1", "LABEL2", "LABEL3"]);

        $statement = (new Query())->set($label)->build();

        $this->assertSame("SET n:LABEL1:LABEL2:LABEL3", $statement);
    }

    public function testWhere(): void
    {
        $expression = $this->getQueryConvertibleMock(BooleanType::class, "a.age");

        $statement = (new Query())->where($expression)->build();

        $this->assertSame("WHERE a.age", $statement);
    }

    public function testWith(): void
    {
        $expression = $this->getQueryConvertibleMock(AnyType::class, "a < b");

        $statement = (new Query())->with($expression)->build();

        $this->assertSame("WITH a < b", $statement);

        $statement = (new Query())->with(["foobar" => $expression])->build();

        $this->assertSame("WITH a < b AS foobar", $statement);
    }

    public function testWithRejectsAnyType(): void
    {
        $m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

        $this->expectException(TypeError::class);

        (new Query())->delete([$m, $m]);
    }

    public function testWithWithNode(): void
    {
        $node = Query::node('m');

        $statement = (new Query())->with($node)->build();

        $this->assertMatchesRegularExpression("/(WITH var[0-9a-f]+)/", $statement);

        $node = Query::node("m");
        $node->setVariable('example');

        $statement = (new Query())->with($node)->build();

        $this->assertSame('WITH example', $statement);
    }

    public function testCallProcedure(): void
    {
        $procedure = "apoc.json";

        $statement = (new Query())->callProcedure($procedure)->build();

        $this->assertSame("CALL apoc.json()", $statement);

        $expression = $this->getQueryConvertibleMock(AnyType::class, "a < b");

        $statement = (new Query())->callProcedure($procedure, [$expression])->build();

        $this->assertSame("CALL apoc.json(a < b)", $statement);

        $variable = $this->getQueryConvertibleMock(Variable::class, "a");

        $statement = (new Query())->callProcedure($procedure, [$expression], [$variable])->build();

        $this->assertSame("CALL apoc.json(a < b) YIELD a", $statement);
    }

    public function testAddClause(): void
    {
        $clauseMockText = "FOOBAR foobar";
        $clauseMock = $this->getQueryConvertibleMock(Clause::class, $clauseMockText);
        $statement = (new Query())->addClause($clauseMock)->build();

        $this->assertSame($clauseMockText, $statement);
    }

    public function testBuild(): void
    {
        $withClause = $this->getQueryConvertibleMock(Clause::class, "WITH foobar");
        $whereClause = $this->getQueryConvertibleMock(Clause::class, "WHERE foobar");

        $query = new Query();
        $query->clauses = [$withClause, $whereClause];

        $statement = $query->build();

        $this->assertSame("WITH foobar WHERE foobar", $statement);

        $nodeMock = $this->getQueryConvertibleMock(Node::class, "(a)");
        $nodeMock->method('getVariable')->willReturn($this->getQueryConvertibleMock(Variable::class, 'a'));

        $variableMock = $this->getQueryConvertibleMock(Variable::class, "a");
        $pathMock = $this->getQueryConvertibleMock(Path::class, "(a)->(b)");
        $numeralMock = $this->getQueryConvertibleMock(NumeralType::class, "12");
        $booleanMock = $this->getQueryConvertibleMock(BooleanType::class, "a > b");
        $propertyMock = $this->getQueryConvertibleMock(Property::class, "a.b");

        $query = new Query();
        $statement = $query->match([$pathMock, $nodeMock])
            ->returning(["#" => $nodeMock])
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

        $this->assertSame("MATCH (a)->(b), (a) RETURN a AS `#` CREATE (a)->(b), (a) CREATE (a)->(b) DELETE a, a DETACH DELETE a, a LIMIT 12 MERGE (a) OPTIONAL MATCH (a), (a) ORDER BY a.b, a.b DESCENDING REMOVE a.b WHERE a > b WITH a AS `#`", $statement);
    }

    public function testBuildEmpty(): void
    {
        $query = new Query();

        $this->assertSame("", $query->build());
    }

    public function testInt(): void
    {
        $literal = Query::literal(1);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1', $literal->toQuery());
    }

    public function testFloat(): void
    {
        $literal = Query::literal(1.2);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1.2', $literal->toQuery());
    }

    public function testString(): void
    {
        $literal = Query::literal('abc');
        self::assertInstanceOf(StringLiteral::class, $literal);
        self::assertEquals("'abc'", $literal->toQuery());
    }

    public function testStringAble(): void
    {
        $literal = Query::literal(new class () {
            public function __toString(): string
            {
                return 'stringable abc';
            }
        });
        self::assertInstanceOf(StringLiteral::class, $literal);
        self::assertEquals("'stringable abc'", $literal->toQuery());
    }

    public function testBool(): void
    {
        $literal = Query::literal(true);
        self::assertInstanceOf(Boolean::class, $literal);
        self::assertEquals("true", $literal->toQuery());
    }

    public function testInvalidLiteral(): void
    {
        $literal = Query::literal(true);
        $this->expectException(InvalidArgumentException::class);
        Query::literal($literal);
    }

    public function testLiteralReference(): void
    {
        $value = Query::literal();

        $this->assertSame(Literal::class, $value);
    }

    public function testUnionQueryAll(): void
    {
        $nodeX = Query::node('X')->setVariable('x');
        $nodeY = Query::node('Y')->setVariable('y');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = $query->union($right, true);

        $this->assertEquals('MATCH (x:X) RETURN x UNION ALL MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionQuery(): void
    {
        $nodeX = Query::node('X')->setVariable('x');
        $nodeY = Query::node('Y')->setVariable('y');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = $query->union($right, false);

        $this->assertEquals('MATCH (x:X) RETURN x UNION MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionDecorator(): void
    {
        $nodeX = Query::node('X')->setVariable('x');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());

        $query = $query->union(function (Query $query) {
            $nodeY = Query::node('Y')->setVariable('y');
            $query->match($nodeY)->returning($nodeY->getVariable());
        });

        $this->assertEquals('MATCH (x:X) RETURN x UNION MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionDecoratorAll(): void
    {
        $nodeX = Query::node('X')->setVariable('x');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());

        $query = $query->union(function (Query $query) {
            $nodeY = Query::node('Y')->setVariable('y');
            $query->match($nodeY)->returning($nodeY->getVariable());
        }, true);

        $this->assertEquals('MATCH (x:X) RETURN x UNION ALL MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testAutomaticIdentifierGeneration(): void
    {
        $node = Query::node();

        $this->assertMatchesRegularExpression('/var[0-9a-f]+\.foo/', $node->property('foo')->toQuery());

        $node->setVariable('foo');

        $this->assertSame('foo.bar', $node->property('bar')->toQuery());

        $node = Query::node();
        $statement = Query::new()->match($node)->returning($node)->build();

        $this->assertMatchesRegularExpression('/MATCH \(var[0-9a-f]+\) RETURN var[0-9a-f]+/', $statement);

        $node = Query::node();

        $this->assertInstanceOf(Variable::class, $node->getVariable());
    }

    public function testCallCallable(): void
    {
        $node = Query::node('X')->setVariable('y');
        $query = Query::new()->match($node)
            ->call(function (Query $subQuery) use ($node) {
                $subQuery->with($node->getVariable())
                    ->where($node->property('z')->equals(Query::literal('foo'), false))
                    ->returning($node->property('z')->alias(Query::variable('foo')));
            })
            ->returning(Query::variable('foo'));

        $this->assertEquals("MATCH (y:X) CALL { WITH y WHERE y.z = 'foo' RETURN y.z AS foo } RETURN foo", $query->toQuery());
    }

    public function testCallClause(): void
    {
        $node = Query::node('X')->setVariable('y');

        $sub = Query::new()->with($node->getVariable())
            ->where($node->property('z')->equals(Query::literal('foo'), false))
            ->returning($node->property('z')->alias(Query::variable('foo')));

        $query = Query::new()
            ->match($node)
            ->call($sub)
            ->returning(Query::variable('foo'));

        $this->assertEquals("MATCH (y:X) CALL { WITH y WHERE y.z = 'foo' RETURN y.z AS foo } RETURN foo", $query->toQuery());
    }

    /**
     * Tests all examples as shown on the wiki (and in the README). If this test fails, that ALWAYS constitutes a major
     * release.
     *
     * @return void
     */
    public function testWikiExamples(): void
    {
        // README.md
        $tom = Query::node("Person")->withProperties(["name" => Query::literal("Tom Hanks")]);
        $coActors = Query::node();

        $statement = Query::new()
            ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
            ->returning($coActors->property("name"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Tom Hanks'})-[:`ACTED_IN`]->()<-[:`ACTED_IN`]-(%s) RETURN %s.name", $statement);

        // CALL clause
        $statement = Query::new()
            ->call(function (Query $query) {
                $name = Query::variable("name");
                $signature = Query::variable("signature");

                $query->callProcedure("dbms.procedures", [], [$name, $signature]);
            })
            ->build();

        $this->assertSame("CALL { CALL dbms.procedures() YIELD name, signature }", $statement);

        $tom = Query::node("Person")->withProperties(["name" => Query::literal("Tom Hanks")]);
        $coActors = Query::node();

        $subQuery = Query::new()
            ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
            ->returning($coActors->property("name"));

        $statement = Query::new()->call($subQuery)->build();

        $this->assertStringMatchesFormat("CALL { MATCH (:Person {name: 'Tom Hanks'})-[:`ACTED_IN`]->()<-[:`ACTED_IN`]-(%s) RETURN %s.name }", $statement);

        // CALL procedure clause
        $statement = Query::new()
            ->callProcedure("apoc.json")
            ->build();

        $this->assertSame("CALL apoc.json()", $statement);

        $name = Query::variable("name");
        $signature = Query::variable("signature");

        $statement = Query::new()
            ->callProcedure("dbms.procedures", [], [$name, $signature])
            ->build();

        $this->assertSame("CALL dbms.procedures() YIELD name, signature", $statement);

        $username = Query::literal("example_username");
        $password = Query::literal("example_password");

        $statement = Query::new()
            ->callProcedure("dbms.security.createUser", [$username, $password, Query::literal(false)])
            ->build();

        $this->assertSame("CALL dbms.security.createUser('example_username', 'example_password', false)", $statement);

        // CREATE clause
        $tom = Query::node("Person")->setVariable("tom");

        $statement = Query::new()
            ->create($tom)
            ->build();

        $this->assertSame("CREATE (tom:Person)", $statement);
    }

    public function provideLiteralData(): array
    {
        return [
            ['foobar', new StringLiteral('foobar')],
            ['0', new StringLiteral('0')],
            ['100', new StringLiteral('100')],
            [0, new Decimal(0)],
            [100, new Decimal(100)],
            [10.0, new Decimal(10.0)],
            [69420, new Decimal(69420)],
            [10.0000000000000000000000000000001, new Decimal(10.0000000000000000000000000000001)],
            [false, new Boolean(false)],
            [true, new Boolean(true)],
        ];
    }
}
