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
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\String_
 */
class StringLiteralTest extends TestCase
{
    public function testEmptySingleQuotes(): void
    {
        $string = new String_("");
        $string->useDoubleQuotes(false);

        $this->assertSame("''", $string->toQuery());
        $this->assertEquals('', $string->getValue());
        $this->assertFalse($string->usesDoubleQuotes());
    }

    public function testEmptyDoubleQuotes(): void
    {
        $string = new String_("");
        $string->useDoubleQuotes(true);

        $this->assertSame('""', $string->toQuery());
        $this->assertEquals('', $string->getValue());
        $this->assertTrue($string->usesDoubleQuotes());
    }

    public function testInstanceOfStringType(): void
    {
        $this->assertInstanceOf(StringType::class, new String_(""));
    }

    /**
     * @dataProvider provideSingleQuotesData
     * @param string $string
     * @param string $expected
     */
    public function testSingleQuotes(string $string, string $expected): void
    {
        $literal = new String_($string);
        $literal->useDoubleQuotes(false);

        $this->assertSame($expected, $literal->toQuery());
        $this->assertEquals($string, $literal->getValue());
        $this->assertFalse($literal->usesDoubleQuotes());
    }

    /**
     * @dataProvider provideDoubleQuotesData
     * @param string $string
     * @param string $expected
     */
    public function testDoubleQuotes(string $string, string $expected): void
    {
        $literal = new String_($string);
        $literal->useDoubleQuotes(true);

        $this->assertSame($expected, $literal->toQuery());
        $this->assertEquals($string, $literal->getValue());
        $this->assertTrue($literal->usesDoubleQuotes());
    }

    public function provideSingleQuotesData(): array
    {
        return [
            ["a", "'a'"],
            ["b", "'b'"],
            ["\t", "'\\t'"],
            ["\b", "'\\\\b'"],
            ["\n", "'\\n'"],
            ["\r", "'\\r'"],
            ["\f", "'\\f'"],
            ["'", "'\\''"],
            ["\"", "'\\\"'"],
            ["\\\\", "'\\\\\\\\'"],
            ["\u1234", "'\\\\u1234'"],
            ["\U12345678", "'\\\\U12345678'"],
            ["\u0000", "'\\\\u0000'"],
            ["\uffff", "'\\\\uffff'"],
            ["\U00000000", "'\\\\U00000000'"],
            ["\Uffffffff", "'\\\\Uffffffff'"],
            ["\\\\b", "'\\\\\\\\b'"],
            ["\t\n\\", "'\\t\\n\\\\'"]
        ];
    }

    public function provideDoubleQuotesData(): array
    {
        return [
            ["a", "\"a\""],
            ["b", "\"b\""],
            ["\t", "\"\\t\""],
            ["\b", "\"\\\\b\""],
            ["\n", "\"\\n\""],
            ["\r", "\"\\r\""],
            ["\f", "\"\\f\""],
            ["'", "\"\\'\""],
            ["\"", "\"\\\"\""],
            ["\\\\", "\"\\\\\\\\\""],
            ["\u1234", "\"\\\\u1234\""],
            ["\U12345678", "\"\\\\U12345678\""],
            ["\u0000", "\"\\\\u0000\""],
            ["\uffff", "\"\\\\uffff\""],
            ["\U00000000", "\"\\\\U00000000\""],
            ["\Uffffffff", "\"\\\\Uffffffff\""],
            ["\\\\b", "\"\\\\\\\\b\""],
            ["\t\n\\", "\"\\t\\n\\\\\""]
        ];
    }
}
