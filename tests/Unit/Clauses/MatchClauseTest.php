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
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MatchClause
 */
final class MatchClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $match = new MatchClause();

        $this->assertSame("", $match->toQuery());
    }

    public function testSinglePattern(): void
    {
        $pattern = Query::node()->withVariable('a');

        $match = new MatchClause();
        $match->addPattern($pattern);

        $this->assertSame("MATCH (a)", $match->toQuery());
    }

    public function testMultiplePatterns(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $match = new MatchClause();
        $match->addPattern($patternA, $patternB);

        $this->assertSame("MATCH (a), (b)", $match->toQuery());
    }

    public function testMultiplePatternsSeparateFunctionCalls(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $match = new MatchClause();
        $match->addPattern($patternA);
        $match->addPattern($patternB);

        $this->assertSame("MATCH (a), (b)", $match->toQuery());
    }

    public function testMultiplePatternsMerge(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');
        $patternC = Query::node()->withVariable('c');
        $patternD = Query::node()->withVariable('d');

        $match = new MatchClause();
        $match->addPattern($patternA, $patternB);
        $match->addPattern($patternC, $patternD);

        $this->assertSame("MATCH (a), (b), (c), (d)", $match->toQuery());
    }

    public function testAddPatternAcceptsAnyMatchablePattern(): void
    {
        $node = Query::node();

        $match = new MatchClause();
        $match->addPattern($node);

        $path = Query::node()->relationshipTo(Query::node());

        $match->addPattern($path);

        $this->assertSame('MATCH (), ()-->()', $match->toQuery());
    }

    public function testAddPatternDoesNotAcceptRelationship(): void
    {
        $rel = Query::relationship(Relationship::DIR_LEFT);

        $match = new MatchClause();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $match->addPattern($rel);
    }

    public function testAddPatternArrayUnpacking(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $patterns = [$patternA, $patternB];

        $match = new MatchClause();
        $match->addPattern(...$patterns);

        $this->assertSame('MATCH (a), (b)', $match->toQuery());
    }

    public function testGetPatterns(): void
    {
        $pattern1 = Query::node();
        $pattern2 = Query::node();

        $match = new MatchClause();
        $match->addPattern($pattern1);
        $match->addPattern($pattern2);

        $this->assertSame([$pattern1, $pattern2], $match->getPatterns());
    }

    public function testAddPatternReturnsSameInstance(): void
    {
        $expected = new MatchClause();
        $actual = $expected->addPattern(Query::node());

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new MatchClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
