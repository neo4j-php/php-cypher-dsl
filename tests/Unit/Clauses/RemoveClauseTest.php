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
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $remove->setPattern($pattern);

        $this->assertSame("REMOVE (a)", $remove->toQuery());
    }
}