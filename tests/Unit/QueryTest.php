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

    public function testNodeWithoutLabel()
    {
        $actual = Query::node();
        $expected = new Node();

        $this->assertEquals($expected, $actual);
    }

    public function testNodeWithLabel()
    {
        $label = "m";

        $actual = Query::node($label);
        $expected = (new Node())->labeled($label);

        $this->assertEquals($expected, $actual);
    }

    public function testRelationship()
    {
        $a = $this->getQueryConvertableMock(NodeType::class, "a");
        $b = $this->getQueryConvertableMock(PathType::class, "b");

        $directions = [Path::DIR_UNI, Path::DIR_LEFT, Path::DIR_RIGHT];

        foreach ($directions as $direction) {
            $expected = new Path($a, $b, $direction);
            $actual = Query::relationship($a, $b, $direction);

            $this->assertEquals($expected, $actual);
        }
    }

    public function testVariable()
    {
        $this->assertInstanceOf(Variable::class, Query::variable("foo"));
    }

    public function testParameter()
    {
        $this->assertInstanceOf(Parameter::class, Query::parameter("foo"));
    }

    /**
     * @dataProvider provideLiteralData
     * @param        $literal
     * @param PropertyType $expected
     */
    public function testLiteral($literal, PropertyType $expected)
    {
        $actual = Query::literal($literal);

        $this->assertEquals($expected, $actual);
    }

    public function testList()
    {
        $list = Query::list([]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfLiterals()
    {
        $list = Query::list(["hello", "world", 1.0, 1, 2, 3, true]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfMixed()
    {
        $list = Query::list([$this->getQueryConvertableMock(AnyType::class, "hello"), "world"]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testListOfAnyType()
    {
        $list = Query::list([$this->getQueryConvertableMock(AnyType::class, "hello"), $this->getQueryConvertableMock(AnyType::class, "world")]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testNestedList()
    {
        $list = Query::list([Query::list([])]);

        $this->assertInstanceOf(ExpressionList::class, $list);
    }

    public function testIteratorList()
    {
        $iterator = new class implements \Iterator {
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

    public function testInvalidList()
    {
        $this->expectException(InvalidArgumentException::class);
        Query::list([new class() {}]);
    }

    public function testMap()
    {
        $map = Query::map([]);

        $this->assertInstanceOf(PropertyMap::class, $map);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFunction()
    {
        Query::function()::raw("test", []);
    }

    public function testMatch()
    {
        $m = $this->getQueryConvertableMock(NodeType::class, "(m:Movie)");

        $statement = (new Query())->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);

        $statement = (new Query())->match([$m, $m])->build();

        $this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
    }

    public function testReturning()
    {
        $m = $this->getQueryConvertableMock(StructuralType::class, "(m:Movie)");

        $statement = (new Query())->returning($m)->build();

        $this->assertSame("RETURN (m:Movie)", $statement);

        $statement = (new Query())->returning(["n" => $m])->build();

        $this->assertSame("RETURN (m:Movie) AS n", $statement);
    }

    public function testReturningWithNode()
    {
        $node = Query::node("(m)");

        $statement = (new Query())->returning($node)->build();

        $this->assertMatchesRegularExpression("/(RETURN [0-Z])\w+/", $statement);
    }

    public function testCreate()
    {
        $m = $this->getQueryConvertableMock(PathType::class, "(m:Movie)->(b)");

        $statement = (new Query())->create($m)->build();

        $this->assertSame("CREATE (m:Movie)->(b)", $statement);

        $statement = (new Query())->create([$m, $m])->build();

        $this->assertSame("CREATE (m:Movie)->(b), (m:Movie)->(b)", $statement);
    }

    public function testDelete()
    {
        $m = $this->getQueryConvertableMock(NodeType::class, "(m:Movie)");

        $statement = (new Query())->delete($m)->build();

        $this->assertSame("DELETE (m:Movie)", $statement);

        $statement = (new Query())->delete([$m, $m])->build();

        $this->assertSame("DELETE (m:Movie), (m:Movie)", $statement);
    }

    public function testDetachDelete()
    {
        $m = $this->getQueryConvertableMock(NodeType::class, "(m:Movie)");

        $statement = (new Query())->detachDelete($m)->build();

        $this->assertSame("DETACH DELETE (m:Movie)", $statement);

        $statement = (new Query())->detachDelete([$m, $m])->build();

        $this->assertSame("DETACH DELETE (m:Movie), (m:Movie)", $statement);
    }

    public function testLimit()
    {
        $expression = $this->getQueryConvertableMock(NumeralType::class, "12");

        $statement = (new Query())->limit($expression)->build();

        $this->assertSame("LIMIT 12", $statement);
    }

    public function testMerge()
    {
        $pattern = $this->getQueryConvertableMock(PathType::class, "(m)->(b)");

        $statement = (new Query())->merge($pattern)->build();

        $this->assertSame("MERGE (m)->(b)", $statement);

        $onCreate = $this->getQueryConvertableMock(Clause::class, "DELETE (m:Movie)");
        $onMatch = $this->getQueryConvertableMock(Clause::class, "CREATE (m:Movie)");

        $statement = (new Query())->merge($pattern, $onCreate, $onMatch)->build();

        $this->assertSame("MERGE (m)->(b) ON CREATE DELETE (m:Movie) ON MATCH CREATE (m:Movie)", $statement);
    }

    public function testOptionalMatch()
    {
        $pattern = $this->getQueryConvertableMock(NodeType::class, "(m)");

        $statement = (new Query())->optionalMatch($pattern)->build();

        $this->assertSame("OPTIONAL MATCH (m)", $statement);

        $statement = (new Query())->optionalMatch([$pattern, $pattern])->build();

        $this->assertSame("OPTIONAL MATCH (m), (m)", $statement);
    }

    public function testOrderBy()
    {
        $property = $this->getQueryConvertableMock(Property::class, "a.foo");

        $statement = (new Query())->orderBy($property)->build();

        $this->assertSame("ORDER BY a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property])->build();

        $this->assertSame("ORDER BY a.foo, a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property], false)->build();

        $this->assertSame("ORDER BY a.foo, a.foo", $statement);

        $statement = (new Query())->orderBy([$property, $property], true)->build();

        $this->assertSame("ORDER BY a.foo, a.foo DESCENDING", $statement);
    }

    public function testRemove()
    {
        $expression = $this->getQueryConvertableMock(Property::class, "a.age");

        $statement = (new Query())->remove($expression)->build();

        $this->assertSame("REMOVE a.age", $statement);
    }

    public function testSet()
    {
        $expression = $this->getQueryConvertableMock(Assignment::class, "a.age");

        $statement = (new Query())->set($expression)->build();

        $this->assertSame("SET a.age", $statement);

        $statement = (new Query())->set([$expression, $expression])->build();

        $this->assertSame("SET a.age, a.age", $statement);
    }

    public function testSetWithLabel()
    {
        $label = Query::variable("n")->labeled(["LABEL1", "LABEL2", "LABEL3"]);

        $statement = (new Query())->set($label)->build();

        $this->assertSame("SET n:LABEL1:LABEL2:LABEL3", $statement);
    }

    public function testWhere()
    {
        $expression = $this->getQueryConvertableMock(BooleanType::class, "a.age");

        $statement = (new Query())->where($expression)->build();

        $this->assertSame("WHERE a.age", $statement);
    }

    public function testWith()
    {
        $expression = $this->getQueryConvertableMock(AnyType::class, "a < b");

        $statement = (new Query())->with($expression)->build();

        $this->assertSame("WITH a < b", $statement);

        $statement = (new Query())->with(["foobar" => $expression])->build();

        $this->assertSame("WITH a < b AS foobar", $statement);
    }

    public function testCallProcedure()
    {
        $procedure = "apoc.json";

        $statement = (new Query())->callProcedure($procedure)->build();

        $this->assertSame("CALL apoc.json()", $statement);

        $expression = $this->getQueryConvertableMock(AnyType::class, "a < b");

        $statement = (new Query())->callProcedure($procedure, [$expression])->build();

        $this->assertSame("CALL apoc.json(a < b)", $statement);

        $variable = $this->getQueryConvertableMock(Variable::class, "a");

        $statement = (new Query())->callProcedure($procedure, [$expression], [$variable])->build();

        $this->assertSame("CALL apoc.json(a < b) YIELD a", $statement);
    }

    public function testAddClause()
    {
        $clauseMockText = "FOOBAR foobar";
        $clauseMock = $this->getQueryConvertableMock(Clause::class, $clauseMockText);
        $statement = (new Query())->addClause($clauseMock)->build();

        $this->assertSame($clauseMockText, $statement);
    }

    public function testBuild()
    {
        $withClause = $this->getQueryConvertableMock(Clause::class, "WITH foobar");
        $whereClause = $this->getQueryConvertableMock(Clause::class, "WHERE foobar");

        $query = new Query();
        $query->clauses = [$withClause, $whereClause];

        $statement = $query->build();

        $this->assertSame("WITH foobar WHERE foobar", $statement);

        $pathMock = $this->getQueryConvertableMock(Path::class, "(a)->(b)");
        $nodeMock = $this->getQueryConvertableMock(Node::class, "(a)");
        $numeralMock = $this->getQueryConvertableMock(NumeralType::class, "12");
        $booleanMock = $this->getQueryConvertableMock(BooleanType::class, "a > b");
        $propertyMock = $this->getQueryConvertableMock(Property::class, "a.b");

        $query = new Query();
        $statement = $query->match([$pathMock, $nodeMock])
            ->returning(["#" => $nodeMock])
            ->create([$pathMock, $nodeMock])
            ->create($pathMock)
            ->delete([$nodeMock, $nodeMock])
            ->detachDelete([$nodeMock, $nodeMock])
            ->limit($numeralMock)
            ->merge($nodeMock)
            ->optionalMatch([$nodeMock, $nodeMock])
            ->orderBy([$propertyMock, $propertyMock], true)
            ->remove($propertyMock)
            ->set([])
            ->where($booleanMock)
            ->with(["#" => $nodeMock])
            ->build();

        $this->assertSame("MATCH (a)->(b), (a) RETURN (a) AS `#` CREATE (a)->(b), (a) CREATE (a)->(b) DELETE (a), (a) DETACH DELETE (a), (a) LIMIT 12 MERGE (a) OPTIONAL MATCH (a), (a) ORDER BY a.b, a.b DESCENDING REMOVE a.b WHERE a > b WITH (a) AS `#`", $statement);
    }

    public function testBuildEmpty()
    {
        $query = new Query();

        $this->assertSame("", $query->build());
    }

    public function testInt()
    {
        $literal = Query::literal(1);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1', $literal->toQuery());
    }

    public function testFloat()
    {
        $literal = Query::literal(1.2);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1.2', $literal->toQuery());
    }

    public function testString()
    {
        $literal = Query::literal('abc');
        self::assertInstanceOf(StringLiteral::class, $literal);
        self::assertEquals("'abc'", $literal->toQuery());
    }

    public function testStringAble()
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

    public function testBool()
    {
        $literal = Query::literal(true);
        self::assertInstanceOf(Boolean::class, $literal);
        self::assertEquals("true", $literal->toQuery());
    }

    public function testInvalidLiteral()
    {
        $literal = Query::literal(true);
        $this->expectException(InvalidArgumentException::class);
        Query::literal($literal);
    }

    public function testLiteralReference()
    {
        $value = Query::literal();

        $this->assertSame(Literal::class, $value);
    }

    public function testWikiExamples()
    {
        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Examples
         */

        $m = Query::variable("m");
        $movie = Query::node("Movie")->named($m);

        $query = new Query();
        $statement = $query->match($movie)
            ->returning($m)
            ->build();

        $this->assertSame("MATCH (m:Movie) RETURN m", $statement);

        $tom = Query::variable("tom");
        $tomHanks = Query::node()->named($tom)->withProperties([
            "name" => Query::literal("Tom Hanks")
        ]);

        $query = new Query();
        $statement = $query->match($tomHanks)
            ->returning($tom)
            ->build();

        $this->assertSame("MATCH (tom {name: 'Tom Hanks'}) RETURN tom", $statement);

        $people = Query::variable("people");
        $person = Query::node("Person")->named($people);

        $statement = Query::new()
            ->match($person)
            ->returning($people->property("name"))
            ->limit(Query::literal(10))
            ->build();

        $this->assertSame("MATCH (people:Person) RETURN people.name LIMIT 10", $statement);

        $nineties = Query::variable("nineties");
        $released = $nineties->property("released");
        $movie = Query::node("Movie")->named($nineties);
        $expression = $released->gte(Query::literal(1990))->and($released->lt(Query::literal(2000)));

        $statement = Query::new()
            ->match($movie)
            ->where($expression)
            ->returning($nineties->property("title"))
            ->build();

        $this->assertSame("MATCH (nineties:Movie) WHERE ((nineties.released >= 1990) AND (nineties.released < 2000)) RETURN nineties.title", $statement);

        $tom = Query::variable("tom");
        $person = Query::node("Person")->withProperties([
            "name" => Query::literal("Tom Hanks")
        ])->named($tom);

        $tomHanksMovies = Query::variable("tomHanksMovies");
        $tomHanksMoviesNode = Query::node()->named($tomHanksMovies);

        $statement = Query::new()
            ->match($person->relationshipTo($tomHanksMoviesNode)->withType("ACTED_IN"))
            ->returning([$tom, $tomHanksMovies])
            ->build();

        $this->assertSame("MATCH (tom:Person {name: 'Tom Hanks'})-[:`ACTED_IN`]->(tomHanksMovies) RETURN tom, tomHanksMovies", $statement);

        $cloudAtlas = Query::variable("cloudAtlas");
        $cloudAtlasNode = Query::node()
            ->named($cloudAtlas)
            ->withProperties(["title" => Query::literal("Cloud Atlas")]);

        $directors = Query::variable("directors");
        $directorsNode = Query::node()->named($directors);

        $statement = Query::new()
            ->match($cloudAtlasNode->relationshipFrom($directorsNode)->withType("DIRECTED"))
            ->returning($directors->property("name"))
            ->build();

        $this->assertSame("MATCH (cloudAtlas {title: 'Cloud Atlas'})<-[:DIRECTED]-(directors) RETURN directors.name", $statement);

        $tom = Query::variable("tom");
        $tomNode = Query::node("Person")->withProperties([
            "name" => Query::literal("Tom Hanks")
        ])->named($tom);

        $movie = Query::variable("m");
        $movieNode = Query::node()->named($movie);

        $coActors = Query::variable("coActors");
        $coActorsNode = Query::node()->named($coActors);

        $statement = Query::new()
            ->match($tomNode->relationshipTo($movieNode)->withType("ACTED_IN")->relationshipFrom($coActorsNode)->withType("ACTED_IN"))
            ->returning($coActors->property("name"))
            ->build();

        $this->assertSame("MATCH (tom:Person {name: 'Tom Hanks'})-[:`ACTED_IN`]->(m)<-[:`ACTED_IN`]-(coActors) RETURN coActors.name", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/CALL-procedure-clause
         */

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

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/CREATE-clause
         */

        $tom = Query::node("Person")->named("tom");

        $statement = Query::new()
            ->create($tom)
            ->build();

        $this->assertSame("CREATE (tom:Person)", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/DELETE-clause
         */

        $tom = Query::node("Person")->named("tom");

        $statement = Query::new()
            ->delete($tom)
            ->build();

        $this->assertSame("DELETE (tom:Person)", $statement);

        $tom = Query::node("Person")->named("tom");

        $statement = Query::new()
            ->detachDelete($tom)
            ->build();

        $this->assertSame("DETACH DELETE (tom:Person)", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/LIMIT-clause
         */

        $statement = Query::new()
            ->limit(Query::literal(10))
            ->build();

        $this->assertSame("LIMIT 10", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/MATCH-clause
         */

        $a = Query::node("a");
        $b = Query::node("b");
        $c = Query::node("c");

        $statement = Query::new()
            ->match([$a, $b, $c])
            ->build();

        $this->assertSame("MATCH (:a), (:b), (:c)", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/MERGE-clause
         */

        $a = Query::node("a");

        $statement = Query::new()
            ->merge($a)
            ->build();

        $this->assertSame("MERGE (:a)", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/OPTIONAL-MATCH-clause
         */

        $a = Query::node("a");
        $b = Query::node("b");

        $statement = Query::new()
            ->optionalMatch([$a, $b])
            ->build();

        $this->assertSame("OPTIONAL MATCH (:a), (:b)", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/ORDER-BY-clause
         */

        $n = Query::variable("n");

        $name = $n->property("name");
        $age = $n->property("age");

        $statement = Query::new()
            ->orderBy([$name, $age], true)
            ->build();

        $this->assertSame("ORDER BY n.name, n.age DESCENDING", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/RAW-clause
         */

        $nested = Query::variable("nested");

        $statement = Query::new()
            ->raw("UNWIND", sprintf("%s AS x", $nested->toQuery()))
            ->build();

        $this->assertSame("UNWIND nested AS x", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/REMOVE-clause
         */

        $n = Query::variable("n")->withLabels(["foo"]);

        $statement = Query::new()
            ->remove($n)
            ->build();

        $this->assertSame("REMOVE n:foo", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/RETURN-clause
         */

        $a = Query::variable("a");
        $b = Query::variable("b");

        $statement = Query::new()
            ->returning([$a, $b], true)
            ->build();

        $this->assertSame("RETURN DISTINCT a, b", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/SET-clause
         */

        $a = Query::variable("a")->assign(Query::literal(10));
        $b = Query::variable("b")->assign(Query::literal(15));

        $statement = Query::new()
            ->set([$a, $b])
            ->build();

        $this->assertSame("SET a = 10, b = 15", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/WHERE-clause
         */

        $expression = Query::variable("n")->gte(Query::literal(10));

        $statement = Query::new()
            ->where($expression)
            ->build();

        $this->assertSame("WHERE (n >= 10)", $statement);

        $variable = Query::variable("n");

        $exists = new Exists((new MatchClause())->addPattern(Query::node()->named($variable)));
        $expression = $exists->and($variable->gte(Query::literal(10)));

        $statement = Query::new()
            ->where($expression)
            ->build();

        $this->assertSame("WHERE (EXISTS { MATCH (n) } AND (n >= 10))", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Clauses/WITH-clause
         */

        $statement = Query::new()
            ->with(["n" => Query::variable("a")])
            ->build();

        $this->assertSame("WITH a AS n", $statement);

        /*
         * @see https://gitlab.wikibase.nl/community/libraries/php-cypher-dsl/-/wikis/Usage/Expressions
         */

        $nineties = Query::variable("nineties");
        $released = $nineties->property("released");

        $expression = $released->gte(Query::literal(1990))->and($released->lt(Query::literal(2000)));

        $this->assertSame("((nineties.released >= 1990) AND (nineties.released < 2000))", $expression->toQuery());

        $actor = Query::variable("actor");
        $name = $actor->property("name");

        $expression = $name->notEquals(Query::literal("Tom Hanks"));

        $this->assertSame("(actor.name <> 'Tom Hanks')", $expression->toQuery());

        $nineties = Query::variable("nineties");
        $released = $nineties->property("released");

        $expression = $released->gte(Query::literal(1990))->and(Query::rawExpression("(nineties IS NOT NULL)"));

        $this->assertSame("((nineties.released >= 1990) AND (nineties IS NOT NULL))", $expression->toQuery());
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
            [true, new Boolean(true)]
        ];
    }
}