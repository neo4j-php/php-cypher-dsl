<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\UnionClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\UnionClause
 */
final class UnionClauseTest extends TestCase
{
    public function testNoCombine(): void
    {
        $union = new UnionClause();

        $this->assertEquals('UNION', $union->toQuery());
    }

    public function testAll(): void
    {
        $union = new UnionClause();
        $union->setAll();

        $this->assertEquals('UNION ALL', $union->toQuery());
    }

    public function testSetAllDefaultIsTrue(): void
    {
        $union = new UnionClause();
        $union->setAll();

        $this->assertSame("UNION ALL", $union->toQuery());
    }

    public function testSetAllCanBeUnset(): void
    {
        $union = new UnionClause();
        $union->setAll();

        $this->assertSame("UNION ALL", $union->toQuery());

        $union->setAll(false);

        $this->assertSame("UNION", $union->toQuery());
    }

    public function testIncludesAll(): void
    {
        $union = new UnionClause();

        $this->assertFalse($union->includesAll());

        $union->setAll();

        $this->assertTrue($union->includesAll());
    }

    public function testSetAllReturnsSameInstance(): void
    {
        $expected = new UnionClause();
        $actual = $expected->setAll();

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new UnionClause();

        $this->assertTrue($clause->canBeEmpty());
    }
}
