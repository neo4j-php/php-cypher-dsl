<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CallClause;
use WikibaseSolutions\CypherDSL\Query;

class CallClauseTest extends TestCase
{
    public function testCallClauseEmpty(): void
    {
        $query = Query::new();

        $clause = new CallClause($query);

        $this->assertEquals('', $clause->toQuery());
        $this->assertEquals(Query::new(), $clause->getSubQuery());
    }

    public function testCallClauseFilled(): void
    {
        $query = Query::new()->match(Query::node('X')->named('x'))->returning(Query::rawExpression('*'));

        $clause = new CallClause($query);

        $this->assertEquals('CALL { MATCH (x:X) RETURN * }', $clause->toQuery());
        $this->assertEquals($query, $clause->getSubQuery());
    }
}
