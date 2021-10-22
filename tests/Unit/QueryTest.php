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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\ExpressionList;
use WikibaseSolutions\CypherDSL\Label;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Parameter;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Variable;
use WikibaseSolutions\CypherDSL\Query;

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
     * @param        PropertyType $expected
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

    public function testMap()
    {
        $map = Query::map([]);

        $this->assertInstanceOf(PropertyMap::class, $map);
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

        $statement =  (new Query())->orderBy([$property, $property], false)->build();

        $this->assertSame("ORDER BY a.foo, a.foo", $statement);

        $statement =  (new Query())->orderBy([$property, $property], true)->build();

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
        $expression = $this->getQueryConvertableMock(AnyType::class, "a.age");

        $statement = (new Query())->set($expression)->build();

        $this->assertSame("SET a.age", $statement);

        $statement = (new Query())->set([$expression, $expression])->build();

        $this->assertSame("SET a.age, a.age", $statement);
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
        $withClause = $this->getQueryConvertableMock(Clause::class,"WITH foobar");
        $whereClause = $this->getQueryConvertableMock(Clause::class,"WHERE foobar");

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