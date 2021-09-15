<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\LimitClause
 */
class LimitClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $limit = new LimitClause();

        $this->assertSame("", $limit->toQuery());
    }

    public function testPattern() {
        $limit = new LimitClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $limit->setExpression($expression);

        $this->assertSame("LIMIT (a)", $limit->toQuery());
    }
}