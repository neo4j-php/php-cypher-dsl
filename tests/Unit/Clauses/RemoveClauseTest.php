<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RemoveClause
 */
class RemoveClauseTest extends TestCase
{
	use TestHelper;

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