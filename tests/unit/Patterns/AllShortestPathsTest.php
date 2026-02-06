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
use WikibaseSolutions\CypherDSL\Patterns\AllShortestPaths;
use WikibaseSolutions\CypherDSL\Query;

final class AllShortestPathsTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $allShortestPaths = new AllShortestPaths($path);

        $this->assertEquals('allShortestPaths(()-->())', $allShortestPaths->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $allShortestPaths = new AllShortestPaths($path);
        $allShortestPaths->withVariable('p');

        $this->assertEquals('p = allShortestPaths(()-->())', $allShortestPaths->toQuery());
    }

    public function testToQueryWithInnerVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $path->withVariable('q');
        $allShortestPaths = new AllShortestPaths($path);

        $this->assertEquals('allShortestPaths(q = ()-->())', $allShortestPaths->toQuery());
    }
}
