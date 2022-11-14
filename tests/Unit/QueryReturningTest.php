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
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "returning" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryReturningTest extends TestCase
{
    public function testReturningNamedNode(): void
    {
        $m = Query::node('Movie')->withVariable('m');

        $statement = Query::new()->returning($m)->build();

        $this->assertSame("RETURN m", $statement);
    }

    public function testReturningUnnamedNodeGeneratesName(): void
    {
        $m = Query::node('Movie');

        $statement = Query::new()->returning($m)->build();

        $this->assertMatchesRegularExpression("/(RETURN var[\da-f]+)/", $statement);
    }

    public function testReturningNamedNodeAlias(): void
    {
        $m = Query::node('Movie')->withVariable('m');

        $statement = Query::new()->returning(["n" => $m])->build();

        $this->assertSame("RETURN m AS n", $statement);
    }

    public function testReturningUnnamedNodeAlias(): void
    {
        $m = Query::node('Movie');

        $statement = Query::new()->returning(["n" => $m])->build();

        $this->assertMatchesRegularExpression("/(RETURN var[\da-f]+ AS n)/", $statement);
    }

    public function testReturnDistinct(): void
    {
        $m = Query::node('Movie')->withVariable('m');

        $statement = Query::new()->returning($m, true)->build();

        $this->assertSame('RETURN DISTINCT m', $statement);
    }

    public function testReturningRejectsNotAnyType(): void
    {
        $m = new class()
        {
        };

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->returning($m);
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->returning(Query::node());

        $this->assertSame($expected, $actual);
    }
}
