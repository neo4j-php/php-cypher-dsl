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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship
 * @package WikibaseSolutions\CypherDSL\Tests\Unit\Patterns
 */
class RelationshipTest extends TestCase
{
	/**
	 * @var MockObject|Pattern
	 */
	private \WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern $a;

	/**
	 * @var MockObject|\WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern
	 */
	private Pattern $b;

	public function setUp(): void
	{
		$this->a = $this->getPatternMock("(a)");
		$this->b = $this->getPatternMock("(b)");
	}

	public function testDirRight()
	{
		$r = new Relationship($this->a, $this->b, Relationship::DIR_RIGHT);
		$this->assertSame("(a)-[]->(b)", $r->toQuery());
	}

	public function testDirLeft()
	{
		$r = new Relationship($this->a, $this->b, Relationship::DIR_LEFT);
		$this->assertSame("(a)<-[]-(b)", $r->toQuery());
	}

	public function testDirUni()
	{
		$r = new Relationship($this->a, $this->b, Relationship::DIR_UNI);
		$this->assertSame("(a)-[]-(b)", $r->toQuery());
	}

	/**
	 * @dataProvider provideWithNameData
	 * @param string $name
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithName(string $name, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->named($name);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithTypeData
	 * @param string $type
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithType(string $type, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->withType($type);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithPropertiesData
	 * @param array $properties
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithProperties(array $properties, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->withProperties($properties);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithNameAndTypeData
	 * @param string $name
	 * @param string $type
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithNameAndType(string $name, string $type, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->named($name)->withType($type);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithNameAndPropertiesData
	 * @param string $name
	 * @param array $properties
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithNameAndProperties(string $name, array $properties, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->named($name)->withProperties($properties);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithTypeAndPropertiesData
	 * @param string $type
	 * @param array $properties
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithTypeAndProperties(string $type, array $properties, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->withType($type)->withProperties($properties);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithNameAndTypeAndPropertiesData
	 * @param string $name
	 * @param string $type
	 * @param array $properties
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithNameAndTypeAndProperties(string $name, string $type, array $properties, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->named($name)->withType($type)->withProperties($properties);

		$this->assertSame($expected, $r->toQuery());
	}

	/**
	 * @dataProvider provideWithMultipleTypesData
	 * @param string $name
	 * @param array $types
	 * @param array $properties
	 * @param array $direction
	 * @param string $expected
	 */
	public function testWithMultipleTypes(string $name, array $types, array $properties, array $direction, string $expected)
	{
		$r = new Relationship($this->a, $this->b, $direction);
		$r->named($name)->withProperties($properties);

		foreach ($types as $type) {
			$r->withType($type);
		}

		$this->assertSame($expected, $r->toQuery());
	}

	public function provideWithNameData(): array
	{
		return [
			['', Relationship::DIR_UNI, '(a)-[]-(b)'],
			['a', Relationship::DIR_UNI, '(a)-[a]-(b)'],
			['a', Relationship::DIR_LEFT, '(a)<-[a]-(b)'],
			[':', Relationship::DIR_RIGHT, '(a)-[`:`]->(b)']
		];
	}

	public function provideWithTypeData(): array
	{
		return [
			['', Relationship::DIR_LEFT, '(a)<-[]-(b)'],
			['a', Relationship::DIR_LEFT, '(a)<-[:a]-(b)'],
			[':', Relationship::DIR_LEFT, '(a)<-[:`:`]-(b)']
		];
	}

	public function provideWithPropertiesData()
	{
		return [
			[[], Relationship::DIR_LEFT, "(a)<-[{}]-(b)"],
			[['a'], Relationship::DIR_LEFT, "(a)<-[{`0`: 'a'}]-(b)"],
			[['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
			[['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[{a: 'b', `0`: 'c'}]-(b)"],
			[[':' => 12], Relationship::DIR_LEFT, "(a)<-[{`:`: 12}]-(b)"]
		];
	}

	public function provideWithNameAndTypeData(): array
	{
		return [
			['', '', Relationship::DIR_LEFT, '(a)<-[]-(b)'],
			['a', '', Relationship::DIR_LEFT, '(a)<-[a]-(b)'],
			['', 'a', Relationship::DIR_LEFT, '(a)<-[:a]-(b)'],
			['a', 'b', Relationship::DIR_LEFT, '(a)<-[a:b]-(b)'],
			[':', 'b', Relationship::DIR_LEFT, '(a)<-[`:`:b]-(b)'],
			[':', ':', Relationship::DIR_LEFT, '(a)<-[`:`:`:`]-(b)']
		];
	}

	public function provideWithNameAndPropertiesData()
	{
		return [
			['a', [], Relationship::DIR_LEFT, "(a)<-[a {}]-(b)"],
			['b', ['a'], Relationship::DIR_LEFT, "(a)<-[b {`0`: 'a'}]-(b)"],
			['', ['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
			[':', ['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[`:` {a: 'b', `0`: 'c'}]-(b)"]
		];
	}

	public function provideWithTypeAndPropertiesData()
	{
		return [
			['a', [], Relationship::DIR_LEFT, "(a)<-[:a {}]-(b)"],
			['b', ['a'], Relationship::DIR_LEFT, "(a)<-[:b {`0`: 'a'}]-(b)"],
			['', ['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[{a: 'b'}]-(b)"],
			[':', ['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[:`:` {a: 'b', `0`: 'c'}]-(b)"]
		];
	}

	public function provideWithNameAndTypeAndPropertiesData()
	{
		return [
			['a', 'a', [], Relationship::DIR_LEFT, "(a)<-[a:a {}]-(b)"],
			['b', 'a', ['a'], Relationship::DIR_LEFT, "(a)<-[b:a {`0`: 'a'}]-(b)"],
			['', 'a', ['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[:a {a: 'b'}]-(b)"],
			[':', 'a', ['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[`:`:a {a: 'b', `0`: 'c'}]-(b)"],
			['a', 'b', ['a'], Relationship::DIR_LEFT, "(a)<-[a:b {`0`: 'a'}]-(b)"],
			['a', '', ['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[a {a: 'b'}]-(b)"],
			['a', ':', ['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[a:`:` {a: 'b', `0`: 'c'}]-(b)"]
		];
	}

	public function provideWithMultipleTypesData()
	{
		return [
			['a', [], [], Relationship::DIR_LEFT, "(a)<-[a {}]-(b)"],
			['b', ['a'], ['a'], Relationship::DIR_LEFT, "(a)<-[b:a {`0`: 'a'}]-(b)"],
			['', ['a', 'b'], ['a' => 'b'], Relationship::DIR_LEFT, "(a)<-[:a|b {a: 'b'}]-(b)"],
			[':', ['a', ':'], ['a' => 'b', 'c'], Relationship::DIR_LEFT, "(a)<-[`:`:a|`:` {a: 'b', `0`: 'c'}]-(b)"],
			['a', ['a', 'b', 'c'], ['a'], Relationship::DIR_LEFT, "(a)<-[a:a|b|c {`0`: 'a'}]-(b)"],
			['a', ['a', 'b'], [], Relationship::DIR_LEFT, "(a)<-[a:a|b {}]-(b)"]
		];
	}

	/**
	 * Creates a mock of the Pattern class that returns the given string when toString() is called.
	 *
	 * @param string $toString
	 * @return \WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern|MockObject
	 */
	private function getPatternMock(string $toString): Pattern
	{
		$mock = $this->getMockBuilder(Pattern::class)->getMock();
		$mock->method('toQuery')->willReturn($toString);

		return $mock;
	}
}