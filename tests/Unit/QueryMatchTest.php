<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * Tests the "match" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryMatchTest extends TestCase
{
	public function testMatch(): void
	{
		$m = $this->getQueryConvertibleMock(NodeType::class, "(m:Movie)");

		$statement = (new Query())->match($m)->build();

		$this->assertSame("MATCH (m:Movie)", $statement);

		$statement = (new Query())->match([$m, $m])->build();

		$this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testMatchTypeAcceptance(): void
	{
		$path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
		$node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

		(new Query())->match([$path, $node]);
	}

	public function testMatchRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->match($m);
	}
}