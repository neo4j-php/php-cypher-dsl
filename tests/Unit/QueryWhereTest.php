<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * Tests the "where" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryWhereTest extends TestCase
{
	public function testWhere(): void
	{
		$expression = new Label(new Variable('a'),'age');

		$statement = (new Query())->where($expression)->build();

		$this->assertSame("WHERE a:age", $statement);
	}
}
