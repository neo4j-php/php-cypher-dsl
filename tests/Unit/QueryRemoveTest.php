<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "remove" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryRemoveTest extends TestCase
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
