<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * Tests the "merge" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryMergeTest extends TestCase
{
	public function testMerge(): void
	{
		$pattern = $this->getQueryConvertibleMock(PathType::class, "(m)->(b)");

		$statement = (new Query())->merge($pattern)->build();

		$this->assertSame("MERGE (m)->(b)", $statement);

		$onCreate = $this->getQueryConvertibleMock(Clause::class, "DELETE (m:Movie)");
		$onMatch = $this->getQueryConvertibleMock(Clause::class, "CREATE (m:Movie)");

		$statement = (new Query())->merge($pattern, $onCreate, $onMatch)->build();

		$this->assertSame("MERGE (m)->(b) ON CREATE DELETE (m:Movie) ON MATCH CREATE (m:Movie)", $statement);
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testMergeTypeAcceptance(): void
	{
		$path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
		$node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

		(new Query())->merge($path);
		(new Query())->merge($node);
	}

	public function testMergeRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->optionalMatch([$m, $m]);
	}
}
