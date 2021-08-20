<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\LimitClause;

class LimitClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $limit = new LimitClause();

        $this->assertSame("", $limit->toQuery());
    }

    public function testPattern() {
        $limit = new LimitClause();
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $limit->setExpression($pattern);

        $this->assertSame("LIMIT (a)", $limit->toQuery());
    }
}