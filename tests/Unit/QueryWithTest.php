<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "with" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryWithTest extends TestCase
{
	public function testWith(): void
	{
		$expression = $this->createMock(AnyType::class);

		$statement = (new Query())->with($expression)->build();

		$this->assertSame("WITH a < b", $statement);

		$statement = (new Query())->with(["foobar" => $expression])->build();

		$this->assertSame("WITH a < b AS foobar", $statement);
	}

	public function testWithWithNode(): void
	{
		$node = Query::node('m');

		$statement = (new Query())->with($node)->build();

		$this->assertMatchesRegularExpression("/(WITH var[0-9a-f]+)/", $statement);

		$node = Query::node("m");
		$node->withVariable('example');

		$statement = (new Query())->with($node)->build();

		$this->assertSame('WITH example', $statement);
	}
}
