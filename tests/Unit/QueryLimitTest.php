<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Tests the "limit" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryLimitTest extends TestCase
{
	public function testLimit(): void
	{
		$expression = $this->getQueryConvertibleMock(NumeralType::class, "12");

		$statement = (new Query())->limit($expression)->build();

		$this->assertSame("LIMIT 12", $statement);
	}
}