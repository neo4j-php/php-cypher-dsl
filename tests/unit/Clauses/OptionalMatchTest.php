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
use WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause
 */
final class OptionalMatchTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $match = new OptionalMatchClause();

        $this->assertSame("", $match->toQuery());
    }

    public function testSinglePattern(): void
    {
        $pattern = Query::node()->withVariable('a');

        $match = new OptionalMatchClause();
        $match->addPattern($pattern);

        $this->assertSame("OPTIONAL MATCH (a)", $match->toQuery());
    }

    public function testMultiplePatterns(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $match = new OptionalMatchClause();
        $match->addPattern($patternA, $patternB);

        $this->assertSame("OPTIONAL MATCH (a), (b)", $match->toQuery());
    }

    public function testMultiplePatternsSeparateFunctionCalls(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $match = new OptionalMatchClause();
        $match->addPattern($patternA);
        $match->addPattern($patternB);

        $this->assertSame("OPTIONAL MATCH (a), (b)", $match->toQuery());
    }

    public function testMultiplePatternsMerge(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');
        $patternC = Query::node()->withVariable('c');
        $patternD = Query::node()->withVariable('d');

        $match = new OptionalMatchClause();
        $match->addPattern($patternA, $patternB);
        $match->addPattern($patternC, $patternD);

        $this->assertSame("OPTIONAL MATCH (a), (b), (c), (d)", $match->toQuery());
    }

    public function testAddPatternAcceptsAnyMatchablePattern(): void
    {
        $node = Query::node();

        $match = new OptionalMatchClause();
        $match->addPattern($node);

        $path = Query::node()->relationshipTo(Query::node());

        $match->addPattern($path);

        $this->assertSame('OPTIONAL MATCH (), ()-->()', $match->toQuery());
    }

    public function testAddPatternDoesNotAcceptRelationship(): void
    {
        $rel = Query::relationship(Direction::LEFT);

        $match = new OptionalMatchClause();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $match->addPattern($rel);
    }

    public function testAddPatternArrayUnpacking(): void
    {
        $patternA = Query::node()->withVariable('a');
        $patternB = Query::node()->withVariable('b');

        $patterns = [$patternA, $patternB];

        $match = new OptionalMatchClause();
        $match->addPattern(...$patterns);

        $this->assertSame('OPTIONAL MATCH (a), (b)', $match->toQuery());
    }

    public function testGetPatterns(): void
    {
        $pattern1 = Query::node();
        $pattern2 = Query::node();

        $match = new OptionalMatchClause();
        $match->addPattern($pattern1);
        $match->addPattern($pattern2);

        $this->assertSame([$pattern1, $pattern2], $match->getPatterns());
    }

    public function testAddPatternReturnsSameInstance(): void
    {
        $expected = new OptionalMatchClause();
        $actual = $expected->addPattern(Query::node());

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new MatchClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
