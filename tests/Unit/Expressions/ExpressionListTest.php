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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\ExpressionList
 */
class ExpressionListTest extends TestCase
{
	public function testEmpty()
	{
		$expressionList = new ExpressionList([]);

		$this->assertSame("[]", $expressionList->toQuery());
	}

	/**
	 * @dataProvider provideOneDimensionalData
	 * @param array $expressions
	 * @param string $expected
	 */
	public function testOneDimensional(array $expressions, string $expected)
	{
		$expressionList = new ExpressionList($expressions);

		$this->assertSame($expected, $expressionList->toQuery());
	}

	/**
	 * @dataProvider provideMultidimensionalData
	 * @param array $expressions
	 * @param string $expected
	 */
	public function testMultidimensional(array $expressions, string $expected)
	{
		$expressionList = new ExpressionList($expressions);

		$this->assertSame($expected, $expressionList->toQuery());
	}

	public function provideOneDimensionalData(): array
	{
		return [
			[[$this->getExpressionMock("12")], "[12]"],
			[[$this->getExpressionMock("'12'")], "['12']"],
			[[$this->getExpressionMock("'12'"), $this->getExpressionMock("'13'")], "['12', '13']"]
		];
	}

	public function provideMultidimensionalData(): array
	{
		return [
			[[new ExpressionList([$this->getExpressionMock("12")])], "[[12]]"],
			[[new ExpressionList([$this->getExpressionMock("'12'")])], "[['12']]"],
			[[new ExpressionList([$this->getExpressionMock("'12'"), $this->getExpressionMock("'14'")]), $this->getExpressionMock("'13'")], "[['12', '14'], '13']"]
		];
	}

	/**
	 * Returns a mock of the Expression class that returns the given string when toQuery() is called.
	 *
	 * @param string $variable
	 * @return Expression|MockObject
	 */
	private function getExpressionMock(string $variable): Expression
	{
		$mock = $this->getMockBuilder(Expression::class)->getMock();
		$mock->method('toQuery')->willReturn($variable);

		return $mock;
	}
}