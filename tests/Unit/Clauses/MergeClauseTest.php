<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MergeClause
 */
class MergeClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $merge = new MergeClause();

        $this->assertSame("", $merge->toQuery());
        $this->assertNull($merge->getOnCreateClause());
        $this->assertNull($merge->getPattern());
        $this->assertNull($merge->getOnMatchClause());
    }

    public function testPattern(): void
    {
        $merge = new MergeClause();
        $pattern = $this->getQueryConvertibleMock(NodeType::class, "(a)");

        $merge->setPattern($pattern);

        $this->assertSame("MERGE (a)", $merge->toQuery());
        $this->assertNull($merge->getOnCreateClause());
        $this->assertEquals($pattern, $merge->getPattern());
        $this->assertNull($merge->getOnMatchClause());
    }

    public function testSetOnCreate(): void
    {
        $merge = new MergeClause();

        $pattern = $this->getQueryConvertibleMock(PathType::class, "(a)-->(b)");
        $clause = $this->getQueryConvertibleMock(Clause::class, "SET a = 10");

        $merge->setPattern($pattern);
        $merge->setOnCreate($clause);

        $this->assertSame("MERGE (a)-->(b) ON CREATE SET a = 10", $merge->toQuery());
        $this->assertEquals($clause, $merge->getOnCreateClause());
        $this->assertEquals($pattern, $merge->getPattern());
        $this->assertNull($merge->getOnMatchClause());
    }

    public function testSetOnMatch(): void
    {
        $merge = new MergeClause();

        $pattern = $this->getQueryConvertibleMock(NodeType::class, "(a)");
        $clause = $this->getQueryConvertibleMock(Clause::class, "SET a = 10");

        $merge->setPattern($pattern);
        $merge->setOnMatch($clause);

        $this->assertSame("MERGE (a) ON MATCH SET a = 10", $merge->toQuery());
        $this->assertNull($merge->getOnCreateClause());
        $this->assertEquals($pattern, $merge->getPattern());
        $this->assertEquals($clause, $merge->getOnMatchClause());
    }

    public function testSetOnBoth(): void
    {
        $merge = new MergeClause();

        $pattern = $this->getQueryConvertibleMock(PathType::class, "(a)-->(b)");
        $clause = $this->getQueryConvertibleMock(Clause::class, "SET a = 10");

        $merge->setPattern($pattern);
        $merge->setOnCreate($clause);
        $merge->setOnMatch($clause);

        $this->assertSame("MERGE (a)-->(b) ON CREATE SET a = 10 ON MATCH SET a = 10", $merge->toQuery());
        $this->assertEquals($clause, $merge->getOnCreateClause());
        $this->assertEquals($pattern, $merge->getPattern());
        $this->assertEquals($clause, $merge->getOnMatchClause());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsNodeType(): void
    {
        $merge = new MergeClause();
        $pattern = $this->getQueryConvertibleMock(NodeType::class, "(a)");

        $merge->setPattern($pattern);

        $merge->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsPathType(): void
    {
        $merge = new MergeClause();
        $pattern = $this->getQueryConvertibleMock(PathType::class, "(a)");

        $merge->setPattern($pattern);

        $merge->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $merge = new MergeClause();
        $pattern = $this->getQueryConvertibleMock(AnyType::class, "(a)");

        $this->expectException(TypeError::class);

        $merge->setPattern($pattern);

        $merge->toQuery();
    }

    public function testGetPattern(): void
    {
        $merge = new MergeClause();
        $pattern = $this->getQueryConvertibleMock(NodeType::class, "(a)");

        $merge->setPattern($pattern);

        $this->assertSame($pattern, $merge->getPattern());
    }

    public function testGetOnCreateClause(): void
    {
        $merge = new MergeClause();
        $onCreateClause = $this->getQueryConvertibleMock(Clause::class, "MATCH a");

        $merge->setOnCreate($onCreateClause);

        $this->assertSame($onCreateClause, $merge->getOnCreateClause());
    }

    public function testGetOnMatch(): void
    {
        $merge = new MergeClause();
        $onMatchClause = $this->getQueryConvertibleMock(Clause::class, "MATCH a");

        $merge->setOnMatch($onMatchClause);

        $this->assertSame($onMatchClause, $merge->getOnMatchClause());
    }
}
