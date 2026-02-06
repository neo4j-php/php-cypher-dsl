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
use WikibaseSolutions\CypherDSL\Patterns\AnyPath;
use WikibaseSolutions\CypherDSL\Query;

final class AnyPathTest extends TestCase
{
    public function testToQuery(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $anyPath = new AnyPath($path);

        $this->assertEquals('ANY (()-->())', $anyPath->toQuery());
    }

    public function testToQueryWithVariable(): void
    {
        $path = Query::node()->relationshipTo(Query::node());
        $anyPath = new AnyPath($path);
        $anyPath->withVariable('p');

        $this->assertEquals('p = ANY (()-->())', $anyPath->toQuery());
    }
}
