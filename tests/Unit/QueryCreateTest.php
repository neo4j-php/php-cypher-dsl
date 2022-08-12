<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * Tests the "create" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryCreateTest extends TestCase
{
	public function testCreate(): void
	{
		$m = $this->getQueryConvertibleMock(PathType::class, "(m:Movie)-[:RELATED]->(b)");

		$statement = (new Query())->create($m)->build();

		$this->assertSame("CREATE (m:Movie)-[:RELATED]->(b)", $statement);

		$statement = (new Query())->create([$m, $m])->build();

		$this->assertSame("CREATE (m:Movie)-[:RELATED]->(b), (m:Movie)-[:RELATED]->(b)", $statement);
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testCreateTypeAcceptance(): void
	{
		$path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
		$node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

		(new Query())->create([$path, $node]);
	}

	public function testCreateRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->create([$m, $m]);
	}
}
