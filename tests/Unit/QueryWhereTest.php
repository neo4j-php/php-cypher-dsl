<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Tests the "where" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryWhereTest extends TestCase
{
	public function testWhere(): void
	{
		$expression = $this->getQueryConvertibleMock(BooleanType::class, "a.age");

		$statement = (new Query())->where($expression)->build();

		$this->assertSame("WHERE a.age", $statement);
	}
}