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
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Variable
 */
class VariableTest extends TestCase
{
	/**
	 * @dataProvider provideToQueryData
	 * @param string $variable
	 * @param string $expected
	 */
	public function testToQuery(string $variable, string $expected)
	{
		$variable = new Variable($variable);

		$this->assertSame($expected, $variable->toQuery());
	}

	/**
	 * @dataProvider providePropertyData
	 * @param string $variable
	 * @param string $property
	 * @param Property $expected
	 */
	public function testProperty(string $variable, string $property, Property $expected)
	{
		$variable = new Variable($variable);
		$property = $variable->property($property);

		$this->assertEquals($expected, $property);
	}

	public function provideToQueryData(): array
	{
		return [
			["a", "a"],
			["b", "b"],
			[":", "`:`"],
			["0", "`0`"]
		];
	}

	public function providePropertyData(): array
	{
		return [
			["a", "a", new Property(new Variable("a"), "a")],
			["a", "b", new Property(new Variable("a"), "b")],
			["0", "0", new Property(new Variable("0"), "0")],
			[":", ":", new Property(new Variable(":"), ":")]
		];
	}
}