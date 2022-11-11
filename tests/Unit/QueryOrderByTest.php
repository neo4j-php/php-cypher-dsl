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

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "orderBy" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryOrderByTest extends TestCase
{
    public function testEmptyArray(): void
    {
        $statement = Query::new()->orderBy([]);

        $this->assertSame("", $statement->toQuery());
    }

    public function testSingleProperty(): void
    {
        $property = Query::variable('a')->property('foo');
        $statement = Query::new()->orderBy($property);

        $this->assertSame("ORDER BY a.foo", $statement->toQuery());
    }

    public function testMultipleProperties(): void
    {
        $a = Query::variable('a')->property('foo');
        $b = Query::variable('b')->property('foo');

        $statement = Query::new()->orderBy([$a, $b]);

        $this->assertSame("ORDER BY a.foo, b.foo", $statement->toQuery());
    }

    public function testDefaultIsNonDescending(): void
    {
        $a = Query::variable('a')->property('foo');

        $default = Query::new()->orderBy($a);
        $explicit = Query::new()->orderBy($a, false);

        $this->assertSame($explicit->toQuery(), $default->toQuery());
    }

    public function testDescending(): void
    {
        $a = Query::variable('a')->property('foo');

        $statement = Query::new()->orderBy($a, true);

        $this->assertSame("ORDER BY a.foo DESCENDING", $statement->toQuery());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->orderBy("foo");
    }

	public function testReturnsSameInstance(): void
	{
		$a = Query::node()->property('a');

		$expected = Query::new();
		$actual = $expected->orderBy($a);

		$this->assertSame($expected, $actual);
	}
}
