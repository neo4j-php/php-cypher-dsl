<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\SetClause;

class SetClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $set = new SetClause();

        $this->assertSame("", $set->toQuery());
    }

    public function testSinglePattern() {
        $set = new SetClause();
        $expression = ClauseTestHelper::getExpressionMock("(a)", $this);

        $set->addExpression($expression);

        $this->assertSame("SET (a)", $set->toQuery());
    }

    public function testMultiplePattern() {
        $set = new SetClause();
        $expressionA = ClauseTestHelper::getExpressionMock("(a)", $this);
        $expressionB = ClauseTestHelper::getExpressionMock("(b)", $this);

        $set->addExpression($expressionA);
        $set->addExpression($expressionB);

        $this->assertSame("SET (a), (b)", $set->toQuery());
    }
}