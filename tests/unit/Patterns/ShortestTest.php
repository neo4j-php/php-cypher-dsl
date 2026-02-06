<?php declare(strict_types=1);

/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Patterns\Shortest;
use WikibaseSolutions\CypherDSL\Query;

final class ShortestTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortest = new Shortest($path, 1);

        $this->assertEquals('SHORTEST 1 (()-->())', $shortest->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortest = new Shortest($path, 5);
        $shortest->withVariable('p');

        $this->assertEquals('p = SHORTEST 5 (()-->())', $shortest->toQuery());
    }

    public function testToQueryWithParameter(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortest = new Shortest($path, Query::parameter('k'));

        $this->assertEquals('SHORTEST $k (()-->())', $shortest->toQuery());
    }

    public function testToQueryWithIntegerClass(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortest = new Shortest($path, Query::integer(10));

        $this->assertEquals('SHORTEST 10 (()-->())', $shortest->toQuery());
    }
}
