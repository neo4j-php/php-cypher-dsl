<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\String_
 */
final class StringTest extends TestCase
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
     */
    public function testDoubleQuotes(string $string, string $expected): void
    {
        $literal = new String_($string);
        $literal->useDoubleQuotes();

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
            ["\t\n\\", "'\\t\\n\\\\'"],
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
            ["\t\n\\", "\"\\t\\n\\\\\""],
        ];
    }
}
