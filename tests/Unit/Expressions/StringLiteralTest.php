<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\StringLiteral;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\StringLiteral
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

	/**
	 * @dataProvider provideSingleQuotesData
	 * @param string $string
	 * @param string $expected
	 */
	public function testSingleQuotes(string $string, string $expected)
	{
		$string = new \WikibaseSolutions\CypherDSL\Expressions\StringLiteral($string);
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