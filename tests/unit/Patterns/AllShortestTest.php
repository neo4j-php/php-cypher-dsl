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
use WikibaseSolutions\CypherDSL\Patterns\AllShortest;
use WikibaseSolutions\CypherDSL\Query;

final class AllShortestTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $allShortest = new AllShortest($path);

        $this->assertEquals('ALL SHORTEST (()-->())', $allShortest->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $allShortest = new AllShortest($path);
        $allShortest->withVariable('p');

        $this->assertEquals('p = ALL SHORTEST (()-->())', $allShortest->toQuery());
    }
}
