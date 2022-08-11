<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CallClause;
use WikibaseSolutions\CypherDSL\Query;

class CallClauseTest extends TestCase
{
    public function testCallClauseWithoutSubqueryIsEmpty(): void
    {
        $clause = new CallClause();

        $this->assertEquals('', $clause->toQuery());
    }

    public function testCallClauseWithEmptySubqueryIsEmpty(): void
    {
        $query = Query::new();

        $clause = new CallClause();
        $clause->withSubQuery($query);

        $this->assertSame('', $clause->toQuery());

		$clause->withVariables(Query::variable('x'));

		$this->assertSame('', $clause->toQuery());
    }

	public function testCallClauseWithoutWithDoesNotHaveWithStatement(): void
	{
		$query = Query::new()->match(Query::node('testing'));

		$clause = new CallClause();
		$clause->withSubQuery($query);

		$this->assertSame('CALL { ' . $query->toQuery() . ' }', $clause->toQuery());
	}

    public function testCallClauseFilled(): void
    {
        $query = Query::new()->match(Query::node('X')->withVariable('x'))->returning(Query::rawExpression('*'));

        $clause = new CallClause();
        $clause->withSubQuery($query);

        $this->assertSame('CALL { MATCH (x:X) RETURN * }', $clause->toQuery());
    }

	public function testCallClauseWithVariables(): void
	{
		$query = Query::new()->match(Query::node('X')->withVariable('x'))->returning(Query::rawExpression('*'));

		$clause = new CallClause();
		$clause->withSubQuery($query);
		$clause->withVariables(Query::variable('x'));

		$this->assertSame('CALL { WITH x MATCH (x:X) RETURN * }', $clause->toQuery());
	}

	public function testAddWithVariable(): void
	{
		$clause = new CallClause();
		$clause->withSubQuery(Query::new()->match(Query::node('x')));

		$clause->addVariable(Query::variable('a'));

		$this->assertSame('CALL { WITH a MATCH (:x) }', $clause->toQuery());

		$clause->addVariable(Query::variable('b'));

		$this->assertSame('CALL { WITH a, b MATCH (:x) }', $clause->toQuery());
	}

	public function testGetSubQuery(): void
	{
		$clause = new CallClause();
		$subQuery = Query::new()->match(Query::node('x'));

		$clause->withSubQuery($subQuery);

		$this->assertSame($subQuery, $clause->getSubQuery());
	}

	public function testGetWithVariables(): void
	{
		$clause = new CallClause();

		$a = Query::variable('a');
		$b = Query::variable('b');
		$c = Query::variable('c');

		$clause->withVariables($a);
		$clause->addVariable($b);
		$clause->addVariable($c);

		$this->assertSame([$a, $b, $c], $clause->getWithVariables());
	}
}
