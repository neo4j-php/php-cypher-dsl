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

    public function testEmptyClause()
    {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
    }

    public function testSingleExpression()
    {
        $remove = new RemoveClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $remove->addExpression($expression);

        $this->assertSame("REMOVE (a)", $remove->toQuery());
    }

    public function testMultipleExpressions()
    {
        $remove = new RemoveClause();

        $a = $this->getExpressionMock("(a)", $this);
        $b = $this->getExpressionMock("(b)", $this);

        $remove->addExpression($a);
        $remove->addExpression($b);

        $this->assertSame("REMOVE (a), (b)", $remove->toQuery());
    }
}