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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Decimal
 */
class DecimalTest extends TestCase
{
    public function testZero()
    {
        $decimal = new Decimal(0);

        $this->assertSame("0", $decimal->toQuery());
    }

    public function testInstanceOfNumeralType()
    {
        $this->assertInstanceOf(NumeralType::class, new Decimal(0));
    }

    /**
     * @dataProvider provideToQueryData
     * @param        $number
     * @param string $expected
     */
    public function testToQuery($number, string $expected)
    {
        $decimal = new Decimal($number);

        $this->assertSame($expected, $decimal->toQuery());
    }

    public function provideToQueryData(): array
    {
        return [
            [1, "1"],
            [2, "2"],
            [3.14, "3.14"],
            [-12, "-12"],
            [69, "69"]
        ];
    }
}