<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Functions\All;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\All
 */
class AllTest extends TestCase
{
	public function testToQuery()
	{
		$variable = $this->getExpressionMock("variable");
		$list = $this->getExpressionMock("list");
		$predicate = $this->getExpressionMock("predicate");

		$all = new All($variable, $list, $predicate);

		$this->assertSame("all(variable IN list WHERE predicate)", $all->toQuery());
	}

	/**
	 * Returns a mock of the Expression class that returns the given string when toQuery() is called.
	 *
	 * @param string $variable
	 * @return Expression|MockObject
	 */
	private function getExpressionMock(string $variable): Expression
	{
		$mock = $this->getMockBuilder(Expression::class)->getMock();
		$mock->method('toQuery')->willReturn($variable);

		return $mock;
	}
}