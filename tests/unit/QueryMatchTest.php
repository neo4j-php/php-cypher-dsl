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
use TypeError;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "match" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryMatchTest extends TestCase
{
    public function testClause(): void
    {
        $m = Query::node('Movie')->withVariable('m');

        $statement = Query::new()->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);

        $statement = Query::new()->match([$m, $m])->build();

        $this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
    }

    public function testDoesNotAcceptRelationship(): void
    {
        $r = Query::relationship(Direction::LEFT);

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->match($r);
    }

    public function testDoesNotAcceptRelationshipWithNode(): void
    {
        $r = Query::relationship(Direction::LEFT);
        $m = Query::node();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->match([$m, $r]);
    }

    public function testDoesNotAcceptTypeOtherThanMatchablePattern(): void
    {
        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->match(false);
    }

    public function testReturnsSameInstance(): void
    {
        $m = Query::node();

        $expected = Query::new();
        $actual = $expected->match($m);

        $this->assertSame($expected, $actual);
    }
}
