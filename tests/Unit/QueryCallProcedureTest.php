<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "callProcedure" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryCallProcedureTest extends TestCase
{
	public function testCallProcedure(): void
	{
		$procedure = "apoc.json";

		$statement = (new Query())->callProcedure($procedure)->build();

		$this->assertSame("CALL apoc.json()", $statement);

		$expression = $this->getQueryConvertibleMock(AnyType::class, "a < b");

		$statement = (new Query())->callProcedure($procedure, [$expression])->build();

		$this->assertSame("CALL apoc.json(a < b)", $statement);

		$variable = $this->getQueryConvertibleMock(Variable::class, "a");

		$statement = (new Query())->callProcedure($procedure, [$expression], [$variable])->build();

		$this->assertSame("CALL apoc.json(a < b) YIELD a", $statement);
	}
}