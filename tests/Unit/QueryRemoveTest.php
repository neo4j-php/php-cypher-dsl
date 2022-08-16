<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "remove" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryRemoveTest
{
	public function testRemove(): void
	{
		$expression = new Property(new Variable('a'),'age');

		$statement = (new Query())->remove($expression)->build();

		$this->assertSame("REMOVE a.age", $statement);
	}

	public function testRemoveRejectsAnyType(): void
	{
		$m = $this->createMock(AnyType::class);

		$this->expectException(TypeError::class);

		(new Query())->remove($m);
	}
}
