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
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use InvalidArgumentException;
/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MatchClause
 */
class MatchClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $match = new MatchClause();

        $this->assertSame("", $match->toQuery());
        $this->assertEquals([], $match->getPatterns());
    }

    public function testSinglePattern(): void
    {
        $match = new MatchClause();
        $pattern = (new Node())->withVariable('a');
        $match->addPattern($pattern);

        $this->assertSame("MATCH (a)", $match->toQuery());
        $this->assertEquals([$pattern], $match->getPatterns());
    }

    public function testMultiplePatterns(): void
    {
        $match = new MatchClause();
        $patternA = (new Node())->withVariable('a');
        $patternB = (new Node())->withVariable('b')->relationshipTo((new Node())->withVariable('c'), 'Foo');

        $match->addPattern($patternA);
        $match->addPattern($patternB);

        $this->assertSame("MATCH (a), (b)-[:Foo]->(c)", $match->toQuery());
        $this->assertEquals([$patternA, $patternB], $match->getPatterns());
    }

    public function testSetPatterns(): void
    {
        $match = new MatchClause();
        $match->addPattern((new Node())->withVariable('c'));

        $patterns = [(new Path([(new Node())->withVariable('a')])), new Path([(new Node())->withVariable('b')])];

        $match->setPatterns($patterns);

        $this->assertSame($patterns, $match->getPatterns());
        $this->assertSame('MATCH (a), (b)', $match->toQuery());
    }

    public function testSetPatternsDoesNotAcceptAnyType(): void
    {
        $match = new MatchClause();

        $this->expectException(InvalidArgumentException::class);

        $match->setPatterns([$this->getQueryConvertibleMock(AnyType::class, "(a)")]);
        $match->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetPatternsAcceptsPath(): void
    {
        $match = new MatchClause();
        $match->setPatterns([new Path([(new Node)->withVariable('a')])]);

        $match->toQuery();
    }

    public function testGetPatterns(): void
    {
        $match = new MatchClause();

        $patterns = [new Path([(new Node)->withVariable('a')])];

        $match->setPatterns($patterns);

        $this->assertSame($patterns, $match->getPatterns());
    }
}
