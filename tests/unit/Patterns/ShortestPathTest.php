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
use WikibaseSolutions\CypherDSL\Patterns\ShortestPath;
use WikibaseSolutions\CypherDSL\Query;

final class ShortestPathTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortestPath = new ShortestPath($path);

        $this->assertEquals('shortestPath(()-->())', $shortestPath->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortestPath = new ShortestPath($path);
        $shortestPath->withVariable('p');

        $this->assertEquals('p = shortestPath(()-->())', $shortestPath->toQuery());
    }

    public function testToQueryWithInnerVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $path->withVariable('q');
        $shortestPath = new ShortestPath($path);

        $this->assertEquals('shortestPath(q = ()-->())', $shortestPath->toQuery());
    }
}
