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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "where" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryWhereTest extends TestCase
{
    public function testWhereSingleExpressionLiteral(): void
    {
        $where = Query::new()->where(true)->toQuery();

        $this->assertSame('WHERE true', $where);
    }

    public function testWhereSingleExpression(): void
    {
        $where = Query::new()->where(Query::literal(true))->toQuery();

        $this->assertSame('WHERE true', $where);
    }

    public function testWhereMultipleExpressions(): void
    {
        $where = Query::new()->where([true, true])->toQuery();

        $this->assertSame('WHERE true AND true', $where);
    }

    public function testDefaultUnifierIsConjunction(): void
    {
        $where = Query::new()->where([true, true])->toQuery();
        $whereAnd = Query::new()->where([true, true], WhereClause::AND)->toQuery();

        $this->assertSame($whereAnd, $where);
    }

    public function testUnifyWithOr(): void
    {
        $where = Query::new()->where([true, true], WhereClause::OR)->toQuery();

        $this->assertSame('WHERE true OR true', $where);
    }

    public function testUnifyWithXor(): void
    {
        $where = Query::new()->where([true, true], WhereClause::XOR)->toQuery();

        $this->assertSame('WHERE true XOR true', $where);
    }

    public function testDoesNotAcceptInvalidOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Query::new()->where([true, true], 'XAND')->toQuery();
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->where(true);

        $this->assertSame($expected, $actual);
    }
}
