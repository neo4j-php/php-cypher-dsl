<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\WhereClause;

class WhereClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $where = new WhereClause();

        $this->assertSame("", $where->toQuery());
    }

    public function testPattern() {
        $where = new WhereClause();
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $where->setPattern($pattern);

        $this->assertSame("WHERE (a)", $where->toQuery());
    }
}