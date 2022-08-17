<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * Tests the "returning" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryReturningTest extends TestCase
{
	public function testReturning(): void
	{
		$m = (new Node("Movie"))->withVariable('m');

		$statement = (new Query())->returning($m)->build();

		$this->assertSame("RETURN m", $statement);

		$statement = (new Query())->returning(["n" => $m])->build();

		$this->assertSame("RETURN m AS n", $statement);
	}

	public function testReturningRejectsNotAnyType(): void
	{
		$m = new class () {};

		$this->expectException(TypeError::class);

		(new Query())->returning($m);
	}

	public function testReturningWithNode(): void
	{
		$node = Query::node("m");

		$statement = (new Query())->returning($node)->build();

		$this->assertMatchesRegularExpression("/(RETURN var[0-9a-f]+)/", $statement);

		$node = Query::node("m");
		$node->withVariable('example');

		$statement = (new Query())->returning($node)->build();

		$this->assertSame('RETURN example', $statement);
	}
}
