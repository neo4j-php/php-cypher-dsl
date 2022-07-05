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
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

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
        $this->expectException(InvalidArgumentException::class);
        Query::literal(Query::literal(true));
    }

    public function testLiteralReference(): void
    {
        $value = Query::literal();

        $this->assertSame(Literal::class, $value);
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
