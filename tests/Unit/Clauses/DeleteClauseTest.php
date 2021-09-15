<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\DeleteClause
 */
class DeleteClauseTest extends TestCase
{
	use ClauseTestHelper;

    public function testEmptyClause() {
        $delete = new DeleteClause();

        $this->assertSame("", $delete->toQuery());
    }

    public function testPattern() {
        $delete = new DeleteClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $delete->setNode($pattern);

        $this->assertSame("DELETE (a)", $delete->toQuery());
    }

    public function testDetachDelete() {
        $delete = new DeleteClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $delete->setNode($pattern);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE (a)", $delete->toQuery());
    }
}