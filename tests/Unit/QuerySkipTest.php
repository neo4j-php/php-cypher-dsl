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
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "skip" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QuerySkipTest extends TestCase
{
    public function testSkipLiteral(): void
    {
        $skip = Query::new()->skip(10)->toQuery();

        $this->assertSame("SKIP 10", $skip);
    }

    public function testSkipInteger(): void
    {
        $skip = Query::new()->skip(Query::integer(10))->toQuery();

        $this->assertSame("SKIP 10", $skip);
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->skip(10);

        $this->assertSame($expected, $actual);
    }
}
