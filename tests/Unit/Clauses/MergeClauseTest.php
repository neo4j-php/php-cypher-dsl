<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MergeClause
 */
class MergeClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $merge = new MergeClause();

        $this->assertSame("", $merge->toQuery());
    }

    public function testPattern()
    {
        $merge = new MergeClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $merge->setPattern($pattern);

        $this->assertSame("MERGE (a)", $merge->toQuery());
    }

    public function testSetOnCreate() {
    	$merge = new MergeClause();

    	$pattern = $this->getPatternMock("(a)", $this);
    	$clause = $this->getClauseMock("SET a = 10", $this);

    	$merge->setPattern($pattern);
    	$merge->setOnCreate($clause);

		$this->assertSame("MERGE (a) ON CREATE SET a = 10", $merge->toQuery());
	}

	public function testSetOnMatch() {
		$merge = new MergeClause();

		$pattern = $this->getPatternMock("(a)", $this);
		$clause = $this->getClauseMock("SET a = 10", $this);

		$merge->setPattern($pattern);
		$merge->setOnMatch($clause);

		$this->assertSame("MERGE (a) ON MATCH SET a = 10", $merge->toQuery());
	}

	public function testSetOnBoth() {
		$merge = new MergeClause();

		$pattern = $this->getPatternMock("(a)", $this);
		$clause = $this->getClauseMock("SET a = 10", $this);

		$merge->setPattern($pattern);
		$merge->setOnCreate($clause);
		$merge->setOnMatch($clause);

		$this->assertSame("MERGE (a) ON CREATE SET a = 10 ON MATCH SET a = 10", $merge->toQuery());
	}
}