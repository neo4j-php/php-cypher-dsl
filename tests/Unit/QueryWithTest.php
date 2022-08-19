<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
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
		$expression = new LessThan(new Variable('a'), new Variable('b'), false);

		$statement = (new Query())->with($expression)->build();

		$this->assertSame("WITH a < b", $statement);

		$statement = (new Query())->with(["foobar" => $expression])->build();

		$this->assertSame("WITH a < b AS foobar", $statement);
	}

	public function testWithWithNode(): void
	{
		$node = Query::node('m');

		$statement = (new Query())->with($node)->build();

		$this->assertMatchesRegularExpression("/(WITH var[\da-f]+)/", $statement);

		$node = Query::node("m");
		$node->withVariable('example');

		$statement = (new Query())->with($node)->build();

		$this->assertSame('WITH example', $statement);
	}
}
