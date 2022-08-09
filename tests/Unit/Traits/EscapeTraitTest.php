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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\EscapeTrait
 */
class EscapeTraitTest extends TestCase
{
    private $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = new class {
			use EscapeTrait {
				escape as public;
			}
		};
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

    public function provideSafeValueIsNotEscapedData(): array
    {
        return [
            ['foobar'],
            ['fooBar'],
            ['FOOBAR'],
            ['foo_bar'],
            ['FOO_BAR'],
            ['aaa'],
            ['aaa100'],
            ['a0'],
            ['z10'],
            ['z99'],
            ['ça'],
            ['日'],
        ];
    }

    public function provideUnsafeValueIsEscapedData(): array
    {
        return [
            [''],
            ['__FooBar__'],
            ['_'],
            ['__'],
            ['\''],
            ['"'],
            ['0'],
            ['10'],
            ['100'],
            ['1'],
            ['2'],
        ];
    }

    /**
     * @dataProvider provideValueWithBacktickIsProperlyEscapedData
     */
    public function testValueWithBacktickIsProperlyEscaped($input, $expected)
    {
        $this->assertSame('`foo``bar`', $this->trait->escape("foo`bar"));
    }

    public function provideValueWithBacktickIsProperlyEscapedData(): array
    {
        return [
            ['foo`bar','`foo``bar`'],
            ['`foo','```foo`'],
            ['foo`','`foo```'],
            ['foo``bar','`foo````bar`'],
            ['`foo`','```foo```'],
        ];
    }
}
