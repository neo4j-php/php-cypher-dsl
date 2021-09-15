<?php


namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryTest extends TestCase
{
	use TestHelper;

	public function testNodeWithoutLabel()
	{
		$actual = Query::node();
		$expected = new Node();

		$this->assertEquals($expected, $actual);
	}

	public function testNodeWithLabel()
	{
		$label = "m";

		$actual = Query::node($label);
		$expected = (new Node())->withLabel($label);

		$this->assertEquals($expected, $actual);
	}

	public function testRelationship()
	{
		$a = $this->getPatternMock("a", $this);
		$b = $this->getPatternMock("b", $this);

		$directions = [Relationship::DIR_UNI, Relationship::DIR_LEFT, Relationship::DIR_RIGHT];

		foreach ($directions as $direction) {
			$expected = new Relationship($a, $b, $direction);
			$actual = Query::relationship($a, $b, $direction);

			$this->assertEquals($expected, $actual);
		}
	}

    public function testMatch() {
        $m = $this->getPatternMock("(m:Movie)", $this);

        $statement = (new Query())->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);
    }

    public function testReturning() {
		$m = $this->getPatternMock("(m:Movie)", $this);

		$statement = (new Query())->returning($m)->build();

		$this->assertSame("RETURN (m:Movie)", $statement);

		$statement = (new Query())->returning($m, "n")->build();

		$this->assertSame("RETURN (m:Movie) AS n", $statement);
	}

	public function testCreate() {
		$m = $this->getPatternMock("(m:Movie)", $this);

		$statement = (new Query())->create($m)->build();

		$this->assertSame("CREATE (m:Movie)", $statement);

		$statement = (new Query())->create([$m, $m])->build();

		$this->assertSame("CREATE (m:Movie), (m:Movie)", $statement);
	}

	public function testDelete() {
		$m = $this->getPatternMock("(m:Movie)", $this);

		$statement = (new Query())->delete($m)->build();

		$this->assertSame("DELETE (m:Movie)", $statement);
	}

	public function testDetachDelete() {
		$m = $this->getPatternMock("(m:Movie)", $this);

		$statement = (new Query())->detachDelete($m)->build();

		$this->assertSame("DETACH DELETE (m:Movie)", $statement);
	}

	public function testLimit() {
		$expression = $this->getExpressionMock("12", $this);

		$statement = (new Query())->limit($expression)->build();

		$this->assertSame("LIMIT 12", $statement);
	}

	public function testMerge() {
		$pattern = $this->getPatternMock("(m)", $this);

		$statement = (new Query())->merge($pattern)->build();

		$this->assertSame("MERGE (m)", $statement);
	}

	public function testOptionalMatch() {
		$pattern = $this->getPatternMock("(m)", $this);

		$statement = (new Query())->optionalMatch($pattern)->build();

		$this->assertSame("OPTIONAL MATCH (m)", $statement);

		$statement = (new Query())->optionalMatch([$pattern, $pattern])->build();

		$this->assertSame("OPTIONAL MATCH (m), (m)", $statement);
	}

	public function testOrderBy() {
		$property = $this->getPropertyMock("a.foo", $this);

		$statement = (new Query())->orderBy($property)->build();

		$this->assertSame("ORDER BY a.foo", $statement);

		$statement = (new Query())->orderBy([$property, $property])->build();

		$this->assertSame("ORDER BY a.foo, a.foo", $statement);

		$statement =  (new Query())->orderBy([$property, $property], false)->build();

		$this->assertSame("ORDER BY a.foo, a.foo", $statement);

		$statement =  (new Query())->orderBy([$property, $property], true)->build();

		$this->assertSame("ORDER BY a.foo, a.foo DESCENDING", $statement);
	}

	public function testRemove() {
		$expression = $this->getExpressionMock("a.age", $this);

		$statement = (new Query())->remove($expression)->build();

		$this->assertSame("REMOVE a.age", $statement);
	}

	public function testSet() {
		$expression = $this->getExpressionMock("a.age", $this);

		$statement = (new Query())->set($expression)->build();

		$this->assertSame("SET a.age", $statement);

		$statement = (new Query())->set([$expression, $expression])->build();

		$this->assertSame("SET a.age, a.age", $statement);
	}

	public function testWhere() {
		$pattern = $this->getPatternMock("(a)", $this);

		$statement = (new Query())->where($pattern)->build();

		$this->assertSame("WHERE (a)", $statement);
	}

	public function testWith() {
		$expression = $this->getExpressionMock("a < b", $this);

		$statement = (new Query())->with($expression)->build();

		$this->assertSame("WITH a < b", $statement);

		$statement = (new Query())->with($expression, "foobar")->build();

		$this->assertSame("WITH a < b AS foobar", $statement);
	}

	public function testBuild() {
		$withClause = $this->getClauseMock("WITH foobar", $this);
		$whereClause = $this->getClauseMock("WHERE foobar", $this);

		$query = (new Query());
		$query->clauses = [$withClause, $whereClause];

		$statement = $query->build();

		$this->assertSame("WITH foobar WHERE foobar", $statement);
	}
}