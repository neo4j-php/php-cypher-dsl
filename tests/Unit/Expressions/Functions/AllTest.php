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
	use FunctionTestHelper;

	public function testToQuery()
	{
		$variable = $this->getExpressionMock("variable", $this);
		$list = $this->getExpressionMock("list", $this);
		$predicate = $this->getExpressionMock("predicate", $this);

		$all = new All($variable, $list, $predicate);

		$this->assertSame("all(variable IN list WHERE predicate)", $all->toQuery());
	}
}