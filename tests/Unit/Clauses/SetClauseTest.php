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
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $set->addPattern($pattern);

        $this->assertSame("SET (a)", $set->toQuery());
    }

    public function testMultiplePattern() {
        $set = new SetClause();
        $patternA = ClauseTestHelper::getPatternMock("(a)", $this);
        $patternB = ClauseTestHelper::getPatternMock("(b)", $this);

        $set->addPattern($patternA);
        $set->addPattern($patternB);

        $this->assertSame("SET (a), (b)", $set->toQuery());
    }
}