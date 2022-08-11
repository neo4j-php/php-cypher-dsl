<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "delete" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryDeleteTest extends TestCase
{
	public function testDelete(): void
	{
		$m = $this->getQueryConvertibleMock(Variable::class, "m");

		$statement = (new Query())->delete($m)->build();

		$this->assertSame("DELETE m", $statement);

		$statement = (new Query())->delete([$m, $m])->build();

		$this->assertSame("DELETE m, m", $statement);
	}

	public function testDeleteRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->delete([$m, $m]);
	}

	public function testDetachDelete(): void
	{
		$m = $this->getQueryConvertibleMock(Variable::class, "m");

		$statement = (new Query())->detachDelete($m)->build();

		$this->assertSame("DETACH DELETE m", $statement);

		$statement = (new Query())->detachDelete([$m, $m])->build();

		$this->assertSame("DETACH DELETE m, m", $statement);
	}

	public function testDetachDeleteRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->detachDelete([$m, $m]);
	}
}
