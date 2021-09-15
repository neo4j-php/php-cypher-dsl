<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;

class WhereClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $where = new WhereClause();

        $this->assertSame("", $where->toQuery());
    }

    public function testPattern() {
        $where = new WhereClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $where->setPattern($pattern);

        $this->assertSame("WHERE (a)", $where->toQuery());
    }
}