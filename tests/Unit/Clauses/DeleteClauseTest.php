<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\DeleteClause
 */
class DeleteClauseTest extends TestCase
{
	use TestHelper;

    public function testEmptyClause() {
        $delete = new DeleteClause();

        $this->assertSame("", $delete->toQuery());
    }

    public function testSingleNode() {
        $delete = new DeleteClause();
        $node = $this->getExpressionMock("(a)", $this);

        $delete->addNode($node);

        $this->assertSame("DELETE (a)", $delete->toQuery());
    }

    public function testMultipleNodes() {
		$delete = new DeleteClause();

		$a = $this->getExpressionMock("(a)", $this);
		$b = $this->getExpressionMock("(b)", $this);

		$delete->addNode($a);
		$delete->addNode($b);

		$this->assertSame("DELETE (a), (b)", $delete->toQuery());
	}

    public function testDetachDelete() {
        $delete = new DeleteClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $delete->addNode($pattern);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE (a)", $delete->toQuery());
    }
}