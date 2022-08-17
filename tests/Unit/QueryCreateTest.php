<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;

/**
 * Tests the "create" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryCreateTest extends TestCase
{
	public function testCreate(): void
	{
		$m = new Path(
                    [(new Node('Movie'))->withVariable('m'), (new Node)->withVariable('b')],
                    [(new Relationship(Relationship::DIR_RIGHT))->addType('RELATED')]
                );

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
		$path = new Path([new Node, new Node], [new Relationship(Relationship::DIR_UNI)]);
		$node = (new Node)->withVariable('a');

		(new Query())->create([$path, $node]);
	}

	public function testCreateRejectsAnyType(): void
	{
		$m = $this->createMock(AnyType::class);

		$this->expectException(TypeError::class);

		(new Query())->create([$m, $m]);
	}
}
