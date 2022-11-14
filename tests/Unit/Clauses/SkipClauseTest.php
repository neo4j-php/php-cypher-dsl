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
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\SkipClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SkipClause
 */
final class SkipClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $skip = new SkipClause();

        $this->assertSame("", $skip->toQuery());
        $this->assertNull($skip->getSkip());
    }

    public function testAcceptsInteger(): void
    {
        $skip = new SkipClause();
        $expression = Query::integer(10);

        $skip->setSkip($expression);

        $this->assertSame("SKIP 10", $skip->toQuery());
    }

    public function testDoesNotAcceptFloat(): void
    {
        $skip = new SkipClause();
        $expression = Query::float(1.0);

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $skip->setSkip($expression);
    }

    public function testGetSkip(): void
    {
        $skip = new SkipClause();
        $expression = Query::integer(10);

        $skip->setSkip($expression);

        $this->assertSame($expression, $skip->getSkip());
    }

    public function testAcceptsLiteralInteger(): void
    {
        $skip = new SkipClause();
        $skip->setSkip(10);

        $this->assertSame("SKIP 10", $skip->toQuery());
    }

    public function testSetSkipReturnsSameInstance(): void
    {
        $expected = new SkipClause();
        $actual = $expected->setSkip(10);

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new SkipClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
