<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SetClause
 */
class SetClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $set = new SetClause();

        $this->assertSame("", $set->toQuery());
    }

    public function testSinglePattern() {
        $set = new SetClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $set->addExpression($expression);

        $this->assertSame("SET (a)", $set->toQuery());
    }

    public function testMultiplePattern() {
        $set = new SetClause();
        $expressionA = $this->getExpressionMock("(a)", $this);
        $expressionB = $this->getExpressionMock("(b)", $this);

        $set->addExpression($expressionA);
        $set->addExpression($expressionB);

        $this->assertSame("SET (a), (b)", $set->toQuery());
    }
}