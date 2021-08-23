<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\MergeClause;

class MergeClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $merge = new MergeClause();

        $this->assertSame("", $merge->toQuery());
    }

    public function testPattern() {
        $merge = new MergeClause();
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $merge->setPattern($pattern);

        $this->assertSame("MERGE (a)", $merge->toQuery());
    }
}