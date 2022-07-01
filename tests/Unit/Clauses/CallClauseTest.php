<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CallClause;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Variable;

class CallClauseTest extends TestCase
{
	use TestHelper;

    public function testCallClauseWithoutSubqueryIsEmpty(): void
    {
        $clause = new CallClause();

        $this->assertEquals('', $clause->toQuery());
    }

    public function testCallClauseWithEmptySubqueryIsEmpty(): void
    {
        $query = Query::new();

        $clause = new CallClause();
        $clause->setSubQuery($query);

        $this->assertSame('', $clause->toQuery());

		$clause->setWithVariables(Query::variable('x'));

		$this->assertSame('', $clause->toQuery());
    }

	public function testCallClauseWithoutWithDoesNotHaveWithStatement(): void
	{
		$query = Query::new()->match(Query::node('testing'));

		$clause = new CallClause();
		$clause->setSubQuery($query);

		$this->assertSame('CALL { ' . $query->toQuery() . ' }', $clause->toQuery());
	}

    public function testCallClauseFilled(): void
    {
        $query = Query::new()->match(Query::node('X')->setVariable('x'))->returning(Query::rawExpression('*'));

        $clause = new CallClause();
        $clause->setSubQuery($query);

        $this->assertSame('CALL { MATCH (x:X) RETURN * }', $clause->toQuery());
    }

	public function testCallClauseWithVariables(): void
	{
		$query = Query::new()->match(Query::node('X')->setVariable('x'))->returning(Query::rawExpression('*'));

		$clause = new CallClause();
		$clause->setSubQuery($query);
		$clause->setWithVariables(Query::variable('x'));

		$this->assertSame('CALL { WITH x MATCH (x:X) RETURN * }', $clause->toQuery());
	}
}
