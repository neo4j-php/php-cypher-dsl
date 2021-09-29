<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CreateClause
 */
class CreateClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $createClause = new CreateClause();

        $this->assertSame("", $createClause->toQuery());
    }

    public function testSinglePattern()
    {
        $createClause = new CreateClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $createClause->addPattern($pattern);

        $this->assertSame("CREATE (a)", $createClause->toQuery());
    }

    public function testMultiplePatterns()
    {
        $createClause = new CreateClause();

        $patternA = $this->getPatternMock("(a)", $this);
        $patternB = $this->getPatternMock("(b)", $this);

        $createClause->addPattern($patternA);
        $createClause->addPattern($patternB);

        $this->assertSame("CREATE (a), (b)", $createClause->toQuery());
    }
}