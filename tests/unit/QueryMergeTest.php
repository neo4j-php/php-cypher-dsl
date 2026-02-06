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
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "merge" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryMergeTest extends TestCase
{
    public function testMergePattern(): void
    {
        $pattern = Query::node()->withVariable('a')->relationshipTo(Query::node()->withVariable('b'));

        $statement = Query::new()->merge($pattern);

        $this->assertSame("MERGE (a)-->(b)", $statement->toQuery());
    }

    public function testMergePatternWithOnCreate(): void
    {
        $pattern = Query::node()->withVariable('a')->relationshipTo(Query::node()->withVariable('b'));
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $statement = Query::new()->merge($pattern, $onCreate);

        $this->assertSame("MERGE (a)-->(b) ON CREATE SET a.a = 'b'", $statement->toQuery());
    }

    public function testMergePatternWithOnMatch(): void
    {
        $pattern = Query::node()->withVariable('a')->relationshipTo(Query::node()->withVariable('b'));
        $onMatch = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $statement = Query::new()->merge($pattern, null, $onMatch);

        $this->assertSame("MERGE (a)-->(b) ON MATCH SET a.a = 'b'", $statement->toQuery());
    }

    public function testMergePatternWithBoth(): void
    {
        $pattern = Query::node()->withVariable('a')->relationshipTo(Query::node()->withVariable('b'));
        $onCreate = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));
        $onMatch = (new SetClause())->add(Query::variable('a')->property('a')->replaceWith('b'));

        $statement = Query::new()->merge($pattern, $onCreate, $onMatch);

        $this->assertSame("MERGE (a)-->(b) ON CREATE SET a.a = 'b' ON MATCH SET a.a = 'b'", $statement->toQuery());
    }

    public function testReturnsSameInstance(): void
    {
        $m = Query::node();

        $expected = Query::new();
        $actual = $expected->merge($m);

        $this->assertSame($expected, $actual);
    }
}
