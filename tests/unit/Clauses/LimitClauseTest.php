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
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\LimitClause
 */
final class LimitClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $limit = new LimitClause();

        $this->assertSame("", $limit->toQuery());
    }

    public function testPattern(): void
    {
        $expression = Query::integer(10);

        $limit = new LimitClause();
        $limit->setLimit($expression);

        $this->assertSame("LIMIT 10", $limit->toQuery());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $expression = Query::string("10");
        $limit = new LimitClause();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $limit->setLimit($expression);
    }

    public function testSetLimitAcceptsPHPInteger(): void
    {
        $limit = new LimitClause();
        $limit->setLimit(10);

        $this->assertSame("LIMIT 10", $limit->toQuery());
    }

    public function testGetLimit(): void
    {
        $limit = new LimitClause();

        $this->assertNull($limit->getLimit());

        $expression = Query::integer(10);

        $limit->setLimit($expression);

        $this->assertSame($expression, $limit->getLimit());
    }

    public function testSetLimitReturnsSameInstance(): void
    {
        $limit = new LimitClause();
        $limit2 = $limit->setLimit(10);

        $this->assertSame($limit, $limit2);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new LimitClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
