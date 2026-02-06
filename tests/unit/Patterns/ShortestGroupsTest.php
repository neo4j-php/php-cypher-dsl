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
use WikibaseSolutions\CypherDSL\Patterns\ShortestGroups;
use WikibaseSolutions\CypherDSL\Query;

final class ShortestGroupsTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortestGroups = new ShortestGroups($path, 2);

        $this->assertEquals('SHORTEST 2 GROUPS (()-->())', $shortestGroups->toQuery());
    }

    public function testToQueryWithIntegerClass(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortestGroups = new ShortestGroups($path, Query::integer(5));

        $this->assertEquals('SHORTEST 5 GROUPS (()-->())', $shortestGroups->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $shortestGroups = new ShortestGroups($path, 10);
        $shortestGroups->withVariable('p');

        $this->assertEquals('p = SHORTEST 10 GROUPS (()-->())', $shortestGroups->toQuery());
    }
}
