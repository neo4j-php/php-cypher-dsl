<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MergeClause
 */
class MergeClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $merge = new MergeClause();

        $this->assertSame("", $merge->toQuery());
    }

    public function testSetPattern(): void
    {
        $pattern = Query::node()->withVariable('a');

        $merge = new MergeClause();
        $merge->setPattern($pattern);

        $this->assertSame("MERGE (a)", $merge->toQuery());
    }

    public function testSetPatternReturnsSameInstance(): void
    {
        $pattern = Query::node()->withVariable('a');

        $expected = new MergeClause();
        $actual = $expected->setPattern($pattern);

        $this->assertSame($expected, $actual);
    }

    public function testSetOnCreate(): void
    {
        $pattern = Query::node()->withVariable('a');
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $merge = new MergeClause();
        $merge->setPattern($pattern);
        $merge->setOnCreate($onCreate);

        $this->assertSame("MERGE (a) ON CREATE SET a.a = 'b'", $merge->toQuery());
    }

    public function testSetOnCreateReturnsSameInstance(): void
    {
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $expected = new MergeClause();
        $actual = $expected->setOnCreate($onCreate);

        $this->assertSame($expected, $actual);
    }

    public function testSetOnMatch(): void
    {
        $pattern = Query::node()->withVariable('a');
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $merge = new MergeClause();
        $merge->setPattern($pattern);
        $merge->setOnMatch($onCreate);

        $this->assertSame("MERGE (a) ON MATCH SET a.a = 'b'", $merge->toQuery());
    }

    public function testSetOnMatchReturnsSameInstance(): void
    {
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $expected = new MergeClause();
        $actual = $expected->setOnMatch($onCreate);

        $this->assertSame($expected, $actual);
    }

    public function testSetOnBoth(): void
    {
        $pattern = Query::node()->withVariable('a');
        $clause = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $merge = new MergeClause();

        $merge->setPattern($pattern);
        $merge->setOnCreate($clause);
        $merge->setOnMatch($clause);

        $this->assertSame("MERGE (a) ON CREATE SET a.a = 'b' ON MATCH SET a.a = 'b'", $merge->toQuery());
    }

    public function testSetPatternDoesNotAcceptRelationship(): void
    {
        $relationship = Query::relationship(Relationship::DIR_RIGHT);

        $merge = new MergeClause();

        $this->expectException(\TypeError::class);

        $merge->setPattern($relationship);
    }

    public function testGetPattern(): void
    {
        $pattern = Query::node()->withVariable('a');

        $merge = new MergeClause();
        $merge->setPattern($pattern);

        $this->assertSame($pattern, $merge->getPattern());
    }

    public function testGetOnCreateClause(): void
    {
        $clause = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $merge = new MergeClause();
        $merge->setOnCreate($clause);

        $this->assertSame($clause, $merge->getOnCreateClause());
    }

    public function testGetOnMatch(): void
    {
        $clause = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $merge = new MergeClause();
        $merge->setOnMatch($clause);

        $this->assertSame($clause, $merge->getOnMatchClause());
    }
}
