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
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Literals\StringLiteral
 */
class StringLiteralTest extends TestCase
{
    public function testEmptySingleQuotes()
    {
        $string = new StringLiteral("");
        $string->useDoubleQuotes(false);

        $this->assertSame("''", $string->toQuery());
    }

    public function testEmptyDoubleQuotes()
    {
        $string = new StringLiteral("");
        $string->useDoubleQuotes(true);

        $this->assertSame('""', $string->toQuery());
    }

    public function testInstanceOfStringType()
    {
        $this->assertInstanceOf(StringType::class, new StringLiteral(""));
    }

    /**
     * @dataProvider provideSingleQuotesData
     * @param string $string
     * @param string $expected
     */
    public function testSingleQuotes(string $string, string $expected)
    {
        $string = new StringLiteral($string);
        $string->useDoubleQuotes(false);

        $this->assertSame($expected, $string->toQuery());
    }

    /**
     * @dataProvider provideDoubleQuotesData
     * @param string $string
     * @param string $expected
     */
    public function testDoubleQuotes(string $string, string $expected)
    {
        $string = new StringLiteral($string);
        $string->useDoubleQuotes(true);

        $this->assertSame($expected, $string->toQuery());
    }

    public function provideSingleQuotesData(): array
    {
        return [
            ["a", "'a'"],
            ["b", "'b'"],
            ["\t", "'\\t'"],
            ["\b", "'\\b'"],
            ["\n", "'\\n'"],
            ["\r", "'\\r'"],
            ["\f", "'\\f'"],
            ["'", "'\\''"],
            ["\"", "'\"'"],
            ["\\\\", "'\\\\'"],
            ["\u1234", "'\\u1234'"],
            ["\U12345678", "'\\U12345678'"],
            ["\u0000", "'\\u0000'"],
            ["\uffff", "'\\uffff'"],
            ["\U00000000", "'\\U00000000'"],
            ["\Uffffffff", "'\\Uffffffff'"],
            ["\\\\b", "'\\\\b'"]
        ];
    }

    public function provideDoubleQuotesData(): array
    {
        return [
            ["a", "\"a\""],
            ["b", "\"b\""],
            ["\t", "\"\\t\""],
            ["\b", "\"\\b\""],
            ["\n", "\"\\n\""],
            ["\r", "\"\\r\""],
            ["\f", "\"\\f\""],
            ["'", "\"'\""],
            ["\"", "\"\\\"\""],
            ["\\\\", "\"\\\\\""],
            ["\u1234", "\"\\u1234\""],
            ["\U12345678", "\"\\U12345678\""],
            ["\u0000", "\"\\u0000\""],
            ["\uffff", "\"\\uffff\""],
            ["\U00000000", "\"\\U00000000\""],
            ["\Uffffffff", "\"\\Uffffffff\""],
            ["\\\\b", "\"\\\\b\""]
        ];
    }
}