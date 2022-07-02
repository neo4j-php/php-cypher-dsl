<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * Tests the "optionalMatch" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryOptionalMatchTest extends TestCase
{
	public function testOptionalMatch(): void
	{
		$pattern = $this->getQueryConvertibleMock(NodeType::class, "(m)");

		$statement = (new Query())->optionalMatch($pattern)->build();

		$this->assertSame("OPTIONAL MATCH (m)", $statement);

		$statement = (new Query())->optionalMatch([$pattern, $pattern])->build();

		$this->assertSame("OPTIONAL MATCH (m), (m)", $statement);
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testOptionalMatchTypeAcceptance(): void
	{
		$path = $this->getQueryConvertibleMock(PathType::class, '(a)-->(b)');
		$node = $this->getQueryConvertibleMock(NodeType::class, '(a)');

		(new Query())->optionalMatch([$path, $node]);
	}

	public function testOptionalMatchRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->optionalMatch([$m, $m]);
	}
}