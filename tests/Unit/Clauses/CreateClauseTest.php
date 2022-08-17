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
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Query;

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

    public function testAddPattern(): void
    {
        $createClause = new CreateClause();

        $createClause->addPattern(Query::node()->withVariable('a'));
        $createClause->addPattern(Query::node()->withVariable('b'));

        $this->assertSame("CREATE (a), (b)", $createClause->toQuery());
    }

    public function testAddPatternDoesNotAcceptAnyType(): void
    {
        $createClause = new CreateClause();

        $this->expectException(TypeError::class);

        $createClause->addPattern(Query::function()::date());
        $createClause->toQuery();
    }

    public function testGetPatterns(): void
    {
        $createClause = new CreateClause();

        $patterns = [
            Query::node('a'),
            Query::node('b')
        ];

        $createClause->addPattern(...$patterns);

        $this->assertSame($patterns, $createClause->getPatterns());

        $patternC = Query::node('c');
        $patterns[] = $patternC;

        $createClause->addPattern($patternC);

        $this->assertSame($patterns, $createClause->getPatterns());
    }
}
