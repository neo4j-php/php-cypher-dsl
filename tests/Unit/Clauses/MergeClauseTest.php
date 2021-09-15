<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MergeClause
 */
class MergeClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $merge = new MergeClause();

        $this->assertSame("", $merge->toQuery());
    }

    public function testPattern() {
        $merge = new MergeClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $merge->setPattern($pattern);

        $this->assertSame("MERGE (a)", $merge->toQuery());
    }
}