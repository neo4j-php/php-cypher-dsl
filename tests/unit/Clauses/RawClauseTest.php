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
use WikibaseSolutions\CypherDSL\Clauses\RawClause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RawClause
 */
final class RawClauseTest extends TestCase
{
    public function testClause(): void
    {
        $raw = new RawClause("UNIMPLEMENTED", "clause body");

        $this->assertSame("UNIMPLEMENTED clause body", $raw->toQuery());
    }

    public function testEmptyClauseIsEmpty(): void
    {
        $raw = new RawClause('', 'body');

        $this->assertSame('', $raw->toQuery());
    }

    public function testCanBeEmpty(): void
    {
        $clause = new RawClause();

        $this->assertFalse($clause->canBeEmpty());
    }
}
