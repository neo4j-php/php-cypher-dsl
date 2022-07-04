<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

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
		$expression = $this->getQueryConvertibleMock(Property::class, "a.age");

		$statement = (new Query())->remove($expression)->build();

		$this->assertSame("REMOVE a.age", $statement);
	}

	public function testRemoveRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->remove($m);
	}
}