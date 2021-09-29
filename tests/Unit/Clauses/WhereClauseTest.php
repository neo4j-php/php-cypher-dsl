<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WhereClause
 */
class WhereClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $where = new WhereClause();

        $this->assertSame("", $where->toQuery());
    }

    public function testExpression()
    {
        $where = new WhereClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $where->setExpression($expression);

        $this->assertSame("WHERE (a)", $where->toQuery());
    }
}