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
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WithClause
 */
final class WithClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $with = new WithClause();

        $this->assertSame("", $with->toQuery());
    }

    public function testSingleEntry(): void
    {
        $with = new WithClause();
        $with->addEntry(Query::variable('a'));

        $this->assertSame("WITH a", $with->toQuery());
    }

    public function testMultipleEntries(): void
    {
        $with = new WithClause();

        $with->addEntry(Query::variable('a'));
        $with->addEntry(Query::variable('b'));
        $with->addEntry(Query::variable('c'));

        $this->assertSame("WITH a, b, c", $with->toQuery());
    }

    public function testSingleAlias(): void
    {
        $with = new WithClause();
        $with->addEntry(Query::variable('a')->alias('b'));

        $this->assertSame("WITH a AS b", $with->toQuery());
    }

    public function testMultipleAliases(): void
    {
        $with = new WithClause();
        $entryA = Query::variable('a')->alias('b');
        $entryB = Query::variable('b')->alias('c');

        $with->addEntry($entryA, $entryB);

        $this->assertSame("WITH a AS b, b AS c", $with->toQuery());
    }

    public function testMixedAliases(): void
    {
        $entryA = Query::variable('a')->alias('b');
        $entryB = Query::variable('c');
        $entryC = Query::variable('b')->alias('c');

        $with = new WithClause();
        $with->addEntry($entryA, $entryB, $entryC);

        $this->assertSame("WITH a AS b, c, b AS c", $with->toQuery());
    }

    public function testAcceptsStringAsVariableName(): void
    {
        $with = new WithClause();
        $with->addEntry('a', 'b', 'c');

        $this->assertSame('WITH a, b, c', $with->toQuery());
    }

    public function testAcceptsPatternAsEntry(): void
    {
        $with = new WithClause();
        $with->addEntry(Query::node());

        $this->assertStringMatchesFormat('WITH %s', $with->toQuery());
    }

    public function testGetEntries(): void
    {
        $entryA = Query::variable('a');
        $entryB = Query::variable('b');

        $with = new WithClause();
        $with->addEntry($entryA, $entryB);

        $this->assertSame([$entryA, $entryB], $with->getEntries());
    }

    public function testAddEntryReturnsSameInstance(): void
    {
        $expected = new WithClause();
        $actual = $expected->addEntry('a');

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new WithClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
