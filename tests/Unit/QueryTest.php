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
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
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
        $expected = (new Node())->withLabel($label);

        $this->assertEquals($expected, $actual);
    }

    public function testRelationship()
    {
        $a = $this->getPatternMock("a", $this);
        $b = $this->getPatternMock("b", $this);

        $directions = [Relationship::DIR_UNI, Relationship::DIR_LEFT, Relationship::DIR_RIGHT];

        foreach ($directions as $direction) {
            $expected = new Relationship($a, $b, $direction);
            $actual = Query::relationship($a, $b, $direction);

            $this->assertEquals($expected, $actual);
        }
    }

    public function testVariable()
    {
        $this->assertInstanceOf(Variable::class, Query::variable("foo"));
    }

    /**
     * @dataProvider provideLiteralData
     * @param        $literal
     * @param        Literal $expected
     */
    public function testLiteral($literal, Literal $expected)
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
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);

        $statement = (new Query())->match([$m, $m])->build();

        $this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
    }

    public function testReturning()
    {
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->returning($m)->build();

        $this->assertSame("RETURN (m:Movie)", $statement);

        $statement = (new Query())->returning($m, "n")->build();

        $this->assertSame("RETURN (m:Movie) AS n", $statement);
    }

    public function testCreate()
    {
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->create($m)->build();

        $this->assertSame("CREATE (m:Movie)", $statement);

        $statement = (new Query())->create([$m, $m])->build();

        $this->assertSame("CREATE (m:Movie), (m:Movie)", $statement);
    }

    public function testDelete()
    {
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->delete($m)->build();

        $this->assertSame("DELETE (m:Movie)", $statement);

        $statement = (new Query())->delete([$m, $m])->build();

        $this->assertSame("DELETE (m:Movie), (m:Movie)", $statement);
    }

    public function testDetachDelete()
    {
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->detachDelete($m)->build();

        $this->assertSame("DETACH DELETE (m:Movie)", $statement);

        $statement = (new Query())->detachDelete([$m, $m])->build();

        $this->assertSame("DETACH DELETE (m:Movie), (m:Movie)", $statement);
    }

    public function testLimit()
    {
        $expression = $this->getExpressionMock("12", $this);

        $statement = (new Query())->limit($expression)->build();

        $this->assertSame("LIMIT 12", $statement);
    }

    public function testMerge()
    {
        $pattern = $this->getPatternMock("(m)", $this);

        $statement = (new Query())->merge($pattern)->build();

        $this->assertSame("MERGE (m)", $statement);
    }

    public function testOptionalMatch()
    {
        $pattern = $this->getPatternMock("(m)", $this);

        $statement = (new Query())->optionalMatch($pattern)->build();

        $this->assertSame("OPTIONAL MATCH (m)", $statement);

        $statement = (new Query())->optionalMatch([$pattern, $pattern])->build();

        $this->assertSame("OPTIONAL MATCH (m), (m)", $statement);
    }

    public function testOrderBy()
    {
        $property = $this->getPropertyMock("a.foo", $this);

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
        $expression = $this->getExpressionMock("a.age", $this);

        $statement = (new Query())->remove($expression)->build();

        $this->assertSame("REMOVE a.age", $statement);
    }

    public function testSet()
    {
        $expression = $this->getExpressionMock("a.age", $this);

        $statement = (new Query())->set($expression)->build();

        $this->assertSame("SET a.age", $statement);

        $statement = (new Query())->set([$expression, $expression])->build();

        $this->assertSame("SET a.age, a.age", $statement);
    }

    public function testWhere()
    {
        $expression = $this->getExpressionMock("(a)", $this);

        $statement = (new Query())->where($expression)->build();

        $this->assertSame("WHERE (a)", $statement);
    }

    public function testWith()
    {
        $expression = $this->getExpressionMock("a < b", $this);

        $statement = (new Query())->with($expression)->build();

        $this->assertSame("WITH a < b", $statement);

        $statement = (new Query())->with($expression, "foobar")->build();

        $this->assertSame("WITH a < b AS foobar", $statement);
    }

    public function testBuild()
    {
        $withClause = $this->getClauseMock("WITH foobar", $this);
        $whereClause = $this->getClauseMock("WHERE foobar", $this);

        $query = new Query();
        $query->clauses = [$withClause, $whereClause];

        $statement = $query->build();

        $this->assertSame("WITH foobar WHERE foobar", $statement);

        $patternMock = $this->getPatternMock("(a)", $this);
        $propertyMock = $this->getPropertyMock("a.b", $this);

        $query = new Query();
        $statement = $query->match([$patternMock, $patternMock])
            ->returning($patternMock, "#")
            ->create([$patternMock, $patternMock])
            ->create($patternMock)
            ->delete([$patternMock, $patternMock])
            ->detachDelete([$patternMock, $patternMock])
            ->limit($patternMock)
            ->merge($patternMock)
            ->optionalMatch([$patternMock, $patternMock])
            ->orderBy([$propertyMock, $propertyMock], true)
            ->remove($patternMock)
            ->set([$patternMock, $patternMock])
            ->where($patternMock)
            ->with($patternMock, "#")
            ->build();

        $this->assertSame("MATCH (a), (a) RETURN (a) AS `#` CREATE (a), (a) CREATE (a) DELETE (a), (a) DETACH DELETE (a), (a) LIMIT (a) MERGE (a) OPTIONAL MATCH (a), (a) ORDER BY a.b, a.b DESCENDING REMOVE (a) SET (a), (a) WHERE (a) WITH (a) AS `#`", $statement);
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