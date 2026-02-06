<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "with" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryWithTest extends TestCase
{
    public function testWithSingleStringVariable(): void
    {
        $with = Query::new()->with('a')->build();

        $this->assertSame('WITH a', $with);
    }

    public function testWithSingleVariable(): void
    {
        $with = Query::new()->with(Query::variable('a'))->build();

        $this->assertSame('WITH a', $with);
    }

    public function testWithMultipleStringVariables(): void
    {
        $with = Query::new()->with(['a', 'b'])->build();

        $this->assertSame('WITH a, b', $with);
    }

    public function testWithMultipleVariables(): void
    {
        $with = Query::new()->with([Query::variable('a'), Query::variable('b')])->build();

        $this->assertSame('WITH a, b', $with);
    }

    public function testWithMultipleVariablesMixed(): void
    {
        $with = Query::new()->with([Query::variable('a'), 'b'])->build();

        $this->assertSame('WITH a, b', $with);
    }

    public function testWithSingleAliasString(): void
    {
        $with = Query::new()->with(['a' => 'b'])->build();

        $this->assertSame('WITH \'b\' AS a', $with);
    }

    public function testWithMultipleAliasesMixed(): void
    {
        $with = Query::new()->with(['a' => 'b', 'b' => Query::variable('c'), 'c'])->build();

        $this->assertSame('WITH \'b\' AS a, c AS b, c', $with);
    }

    public function testMultipleWith(): void
    {
        $with = Query::new()->with('a')->with('b')->build();

        $this->assertSame('WITH a WITH b', $with);
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->with(new class
        {
        });
    }

    public function testInteger(): void
    {
        $with = Query::new()->with(['a' => 25])->build();

        $this->assertSame('WITH 25 AS a', $with);
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->with('a');

        $this->assertSame($expected, $actual);
    }
}
