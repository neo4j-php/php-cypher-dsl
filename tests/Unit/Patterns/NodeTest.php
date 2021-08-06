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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Patterns\Node;

/**
 * @covers \WikibaseSolutions\CypherDSL\Node
 * @package WikibaseSolutions\CypherDSL\Tests\Unit\Patterns
 */
class NodeTest extends TestCase
{
	public function testEmptyNode()
	{
		$node = new Node();

		$this->assertSame( "()", $node->toString() );
	}

	/**
	 * @dataProvider provideOnlyLabelData
	 * @param string $label
	 * @param string $expected
	 */
	public function testOnlyLabel(string $label, string $expected)
	{
		$node = new Node();
		$node->withLabel($label);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideOnlyNameData
	 * @param string $name
	 * @param string $expected
	 */
	public function testOnlyName(string $name, string $expected)
	{
		$node = new Node();
		$node->named($name);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideOnlyPropertiesData
	 * @param array $properties
	 * @param string $expected
	 */
	public function testOnlyProperties(array $properties, string $expected)
	{
		$node = new Node();
		$node->withProperties($properties);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideWithNameAndLabelData
	 * @param string $name
	 * @param string $label
	 * @param string $expected
	 */
	public function testWithNameAndLabel(string $name, string $label, string $expected)
	{
		$node = new Node();
		$node->withLabel($label)->named($name);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideWithNameAndPropertiesData
	 * @param string $name
	 * @param array $properties
	 * @param string $expected
	 */
	public function testWithNameAndProperties(string $name, array $properties, string $expected)
	{
		$node = new Node();
		$node->named($name)->withProperties($properties);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideWithLabelAndPropertiesData
	 * @param string $label
	 * @param array $properties
	 * @param string $expected
	 */
	public function testWithLabelAndProperties(string $label, array $properties, string $expected)
	{
		$node = new Node();
		$node->withLabel($label)->withProperties($properties);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideWithNameAndLabelAndPropertiesData
	 * @param string $name
	 * @param string $label
	 * @param array $properties
	 * @param string $expected
	 */
	public function testWithNameAndLabelAndProperties(string $name, string $label, array $properties, string $expected)
	{
		$node = new Node();
		$node->named($name)->withLabel($label)->withProperties($properties);

		$this->assertSame($expected, $node->toString());
	}

	/**
	 * @dataProvider provideBacktickThrowsExceptionData
	 * @param Node $invalidNode
	 */
	public function testBacktickThrowsException(Node $invalidNode)
	{
		$this->expectException(\InvalidArgumentException::class);
		$invalidNode->toString();
	}

	public function testSetterSameAsConstructor()
	{
		$label = "__test__";
		$viaConstructor = new Node($label);
		$viaSetter = (new Node())->withLabel($label);

		$this->assertSame($viaConstructor->toString(), $viaSetter->toString(), "Setting label via setter has different effect than using constructor");
	}

	public function provideOnlyLabelData(): array
	{
		return [
			['a', '(:a)'],
			['A', '(:A)'],
			[':', '(:`:`)']
		];
	}

	public function provideOnlyNameData(): array
	{
		return [
			['a', '(a)'],
			['A', '(A)'],
			[':', '(`:`)']
		];
	}

	public function provideBacktickThrowsExceptionData(): array
	{
		return [
			[new Node('__`__')],
			[(new Node())->named('__`__')],
			[(new Node())->withProperties(['__`__' => ""])]
		];
	}

	public function provideWithNameAndLabelData(): array
	{
		return [
			['a', 'a', '(a:a)'],
			['A', ':', '(A:`:`)'],
			['', 'b', '(:b)']
		];
	}

	public function provideWithNameAndPropertiesData()
	{
		return [
			['a', ['a' => 'b', 'b' => 'c'], "(a {a: 'b', b: 'c'})"],
			['b', ['a' => 0, 'b' => 1], "(b {a: 0, b: 1})"],
			['c', [':' => [1,'a']], "(c {`:`: [1, 'a']})"]
		];
	}

	public function provideWithLabelAndPropertiesData()
	{
		return [
			['a', ['a' => 'b', 'b' => 'c'], "(:a {a: 'b', b: 'c'})"],
			['b', ['a' => 0, 'b' => 1], "(:b {a: 0, b: 1})"],
			['c', [':' => [1,'a']], "(:c {`:`: [1, 'a']})"]
		];
	}

	public function provideOnlyPropertiesData()
	{
		return [
			[['a' => 'b', 'b' => 'c'], "({a: 'b', b: 'c'})"],
			[['a' => 0, 'b' => 1], "({a: 0, b: 1})"],
			[[':' => [1,'a']], "({`:`: [1, 'a']})"]
		];
	}

	public function provideWithNameAndLabelAndPropertiesData()
	{
		return [
			['a', 'd', ['a' => 'b', 'b' => 'c'], "(a:d {a: 'b', b: 'c'})"],
			['b', 'e', ['a' => 0, 'b' => 1], "(b:e {a: 0, b: 1})"],
			['c', 'f', [':' => [1,'a']], "(c:f {`:`: [1, 'a']})"]
		];
	}
}