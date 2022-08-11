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
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CreateClause
 */
class CreateClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $createClause = new CreateClause();

        $this->assertSame("", $createClause->toQuery());
        $this->assertEquals([], $createClause->getPatterns());
    }

    public function testSinglePattern(): void
    {
        $createClause = new CreateClause();
        $pattern = Query::node()->withVariable('a');

        $createClause->addPattern($pattern);

        $this->assertSame("CREATE (a)", $createClause->toQuery());
        $this->assertEquals([$pattern], $createClause->getPatterns());
    }

    public function testMultiplePatterns(): void
    {
        $createClause = new CreateClause();

        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b')->relationshipTo(Query::node()->withVariable('c'), 'Foo');

        $createClause->addPattern($patternA);
        $createClause->addPattern($patternB);

        $this->assertSame("CREATE (a), (b)-[:Foo]->(c)", $createClause->toQuery());
        $this->assertEquals([$patternA, $patternB], $createClause->getPatterns());
    }

    public function testSetPatterns(): void
    {
        $createClause = new CreateClause();

        $pathExpression = Query::node()->relationshipTo(Query::node());
        $createClause->addPattern($pathExpression);

        $createClause->setPatterns([Query::node()->withVariable('a'), Query::node()->withVariable('b')]);

        $this->assertSame("CREATE (a), (b)", $createClause->toQuery());
    }

    public function testAddPattern(): void
    {
        $createClause = new CreateClause();

        $createClause->addPattern(Query::node()->withVariable('a'));
        $createClause->addPattern(Query::node()->withVariable('b'));

        $this->assertSame("CREATE (a), (b)", $createClause->toQuery());
    }

    public function testSetPatternsDoesNotAcceptAnyType(): void
    {
        $createClause = new CreateClause();

        $this->expectException(TypeError::class);

        $createClause->setPatterns([Query::function()::date()]);
        $createClause->toQuery();
    }

    public function testGetPatterns(): void
    {
        $createClause = new CreateClause();

        $patterns = [
            Query::node('a'),
            Query::node('b')
        ];

        $createClause->setPatterns($patterns);

        $this->assertSame($patterns, $createClause->getPatterns());

        $patternC = Query::node('c');
        $patterns[] = $patternC;

        $createClause->addPattern($patternC);

        $this->assertSame($patterns, $createClause->getPatterns());
    }
}
