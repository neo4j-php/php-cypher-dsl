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
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;

/**
 * Tests the "limit" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryLimitTest extends TestCase
{
    public function testClause(): void
    {
        $expression = Query::integer(12);

        $statement = (new Query())->limit($expression)->build();

        $this->assertSame("LIMIT 12", $statement);
    }

    public function testWithExpression(): void
    {
        $expression = Query::integer(12)->plus(Query::integer(16));

        $statement = (new Query())->limit($expression)->build();

        $this->assertSame("LIMIT (12 + 16)", $statement);
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $this->expectException(TypeError::class);

        (new Query())->limit("10")->build();
    }

    public function testWithPHPInteger(): void
    {
        $statement = (new Query())->limit(12)->build();

        $this->assertSame("LIMIT 12", $statement);
    }

	public function testReturnsSameInstance(): void
	{
		$expected = Query::new();
		$actual = $expected->limit(420);

		$this->assertSame($expected, $actual);
	}
}
