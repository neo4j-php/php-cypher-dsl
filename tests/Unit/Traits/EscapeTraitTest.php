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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\EscapeTrait
 */
class EscapeTraitTest extends TestCase
{
	/**
	 * @var MockObject|EscapeTrait
	 */
	private MockObject $trait;

	public function setUp(): void
	{
		parent::setUp();

		$this->trait = $this->getMockForTrait(EscapeTrait::class);
	}

	/**
	 * @param string $expected
	 * @dataProvider provideSafeValueIsNotEscapedData
	 */
	public function testSafeValueIsNotEscaped(string $expected)
	{
		$actual = $this->trait->escape($expected);

		$this->assertSame($expected, $actual);
	}

	/**
	 * @param string $value
	 * @dataProvider provideUnsafeValueIsEscapedData
	 */
	public function testUnsafeValueIsEscaped(string $value)
	{
		$expected = sprintf("`%s`", $value);
		$actual = $this->trait->escape($value);

		$this->assertSame($expected, $actual);
	}

	public function testValueWithBacktickThrowsException()
	{
		$this->expectException(InvalidArgumentException::class);

		$this->trait->escape("foo`bar");
	}

	public function provideSafeValueIsNotEscapedData(): array
	{
		return [
			['foobar'],
			['fooBar'],
			['FOOBAR'],
			['aaa'],
			['bbb'],
			['ccc'],
			['ddd'],
			['eee'],
			['fff'],
			['ggg'],
			['hhh'],
			['iii'],
			['jjj'],
			['kkk'],
			['lll'],
			['mmm'],
			['nnn'],
			['ooo'],
			['ppp'],
			['qqq'],
			['rrr'],
			['sss'],
			['ttt'],
			['uuu'],
			['vvv'],
			['www'],
			['xxx'],
			['yyy'],
			['zzz'],
			['']
		];
	}

	public function provideUnsafeValueIsEscapedData(): array
	{
		return [
			['foo_bar'],
			['FOO_BAR'],
			['_'],
			['__'],
			['\''],
			['"']
		];
	}
}