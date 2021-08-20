<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use WikibaseSolutions\CypherDSL\Clauses\CreateClause;

class CreateClauseTest extends \PHPUnit\Framework\TestCase
{
    public function testEmptyClause() {
        $createClause = new CreateClause();

        $this->assertSame("", $createClause->toQuery());
    }

    public function testSinglePattern() {
        $createClause = new CreateClause();
        $pattern = ClauseTestHelper::getPatternMock("(a)", $this);

        $createClause->addPattern($pattern);

        $this->assertSame("CREATE (a)", $createClause->toQuery());
    }

    public function testMultiplePatterns() {
        $createClause = new CreateClause();

        $patternA = ClauseTestHelper::getPatternMock("(a)", $this);
        $patternB = ClauseTestHelper::getPatternMock("(b)", $this);

        $createClause->addPattern($patternA);
        $createClause->addPattern($patternB);

        $this->assertSame("CREATE (a), (b)", $createClause->toQuery());
    }
}