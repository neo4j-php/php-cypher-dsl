<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "orderBy" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryOrderByTest extends TestCase
{
	public function testOrderBy(): void
	{
		$property = $this->getQueryConvertibleMock(Property::class, "a.foo");

		$statement = (new Query())->orderBy($property)->build();

		$this->assertSame("ORDER BY a.foo", $statement);

		$statement = (new Query())->orderBy([$property, $property])->build();

		$this->assertSame("ORDER BY a.foo, a.foo", $statement);

		$statement = (new Query())->orderBy([$property, $property], false)->build();

		$this->assertSame("ORDER BY a.foo, a.foo", $statement);

		$statement = (new Query())->orderBy([$property, $property], true)->build();

		$this->assertSame("ORDER BY a.foo, a.foo DESCENDING", $statement);
	}

	public function testOrderByRejectsAnyType(): void
	{
		$m = $this->getQueryConvertibleMock(AnyType::class, 'foo');

		$this->expectException(TypeError::class);

		(new Query())->orderBy([$m, $m]);
	}

}