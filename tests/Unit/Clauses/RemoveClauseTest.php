<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;

class RemoveClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
    }

    public function testPattern() {
        $remove = new RemoveClause();
        $expression = ClauseTestHelper::getExpressionMock("(a)", $this);

        $remove->setExpression($expression);

        $this->assertSame("REMOVE (a)", $remove->toQuery());
    }
}