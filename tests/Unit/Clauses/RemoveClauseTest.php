<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RemoveClause
 */
class RemoveClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
    }

    public function testPattern() {
        $remove = new RemoveClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $remove->setExpression($expression);

        $this->assertSame("REMOVE (a)", $remove->toQuery());
    }
}