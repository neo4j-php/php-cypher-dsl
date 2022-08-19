<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\Clause;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\Clause
 */
final class ClauseTest extends TestCase
{
    public function setUp(): void
    {
        $this->clause = $this->getMockForAbstractClass(Clause::class);
    }

    public function testCanBeEmptyDefaultFalse(): void
    {
        $this->assertFalse($this->clause->canBeEmpty());
    }

    public function testEmptyClauseReturnsEmptyQuery(): void
    {
        $this->clause->method('getClause')->willReturn('');
        $this->clause->method('getSubject')->willReturn('body');

        $this->assertSame('', $this->clause->toQuery());
    }

    public function testEmptySubjectReturnsEmptyQueryIfCannotBeEmpty(): void
    {
        $this->clause->method('getClause')->willReturn('CLAUSE');
        $this->clause->method('getSubject')->willReturn('');

        $this->assertSame('', $this->clause->toQuery());
    }

    public function testToQuery(): void
    {
        $this->clause->method('getClause')->willReturn('CLAUSE');
        $this->clause->method('getSubject')->willReturn('body');

        $this->assertSame('CLAUSE body', $this->clause->toQuery());
    }
}