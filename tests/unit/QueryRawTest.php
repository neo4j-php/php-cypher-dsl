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
 * Tests the "raw" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryRawTest extends TestCase
{
    public function testClause(): void
    {
        $query = Query::new()->raw('UNIMPLEMENTED', 'clause body');

        $this->assertSame('UNIMPLEMENTED clause body', $query->toQuery());
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->raw('UNIMPLEMENTED', 'body');

        $this->assertSame($expected, $actual);
    }
}
