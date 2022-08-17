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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Integer
 */
class IntegerTest extends TestCase
{
    public function testZero(): void
    {
        $decimal = new Integer(0);

        $this->assertSame("0", $decimal->toQuery());
        $this->assertEquals('0', $decimal->getValue());
    }

    public function testInstanceOfNumeralType(): void
    {
        $this->assertInstanceOf(NumeralType::class, new Integer(0));
    }

    /**
     * @dataProvider provideToQueryData
     * @param        $number
     * @param string $expected
     */
    public function testToQuery($number, string $expected): void
    {
        $decimal = new Integer($number);

        $this->assertSame($expected, $decimal->toQuery());
        $this->assertEquals($expected, $decimal->getValue());
    }

    public function provideToQueryData(): array
    {
        return [
            [1, "1"],
            [2, "2"],
            ["2147483649", "2147483649"],
            ["9223372036854775816", "9223372036854775816"],
            [-12, "-12"],
            [69, "69"],
        ];
    }
}
