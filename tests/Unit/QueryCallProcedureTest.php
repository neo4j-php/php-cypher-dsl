<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "callProcedure" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryCallProcedureTest extends TestCase
{
	public function testOnlyProcedure(): void
	{
		$procedure = Procedure::localtime();

		$statement = Query::new()->callProcedure($procedure);

		$this->assertSame("CALL localtime()", $statement->toQuery());
	}

	public function testProcedureWithVariableYield(): void
	{
		$procedure = Procedure::localtime();

		$statement = Query::new()->callProcedure($procedure, Query::variable('a'));

		$this->assertSame("CALL localtime() YIELD a", $statement->toQuery());
	}

	public function testProcedureWithStringYield(): void
	{
		$procedure = Procedure::localtime();

		$statement = Query::new()->callProcedure($procedure, 'a');

		$this->assertSame("CALL localtime() YIELD a", $statement->toQuery());
	}

	public function testProcedureWithMultipleYields(): void
	{
		$procedure = Procedure::localtime();

		$statement = Query::new()->callProcedure($procedure, ['a', Query::variable('b')]);

		$this->assertSame("CALL localtime() YIELD a, b", $statement->toQuery());
	}

	public function testReturnsSameInstance(): void
	{
		$procedure = Procedure::localtime();

		$expected = Query::new();
		$actual = $expected->callProcedure($procedure);

		$this->assertSame($expected, $actual);
	}
}