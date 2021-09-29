<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Decimal
 */
class DecimalTest extends TestCase
{
	public function testZero()
	{
		$decimal = new Decimal(0);

		$this->assertSame("0", $decimal->toQuery());
	}

	/**
	 * @dataProvider provideToQueryData
	 * @param $number
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