<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\LimitClause
 */
class LimitClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $limit = new LimitClause();

        $this->assertSame("", $limit->toQuery());
    }

    public function testPattern()
    {
        $limit = new LimitClause();
        $expression = $this->getExpressionMock("10", $this);

        $limit->setExpression($expression);

        $this->assertSame("LIMIT 10", $limit->toQuery());
    }
}