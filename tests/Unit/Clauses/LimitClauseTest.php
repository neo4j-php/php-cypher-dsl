<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\LimitClause
 */
class LimitClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $limit = new LimitClause();

        $this->assertSame("", $limit->toQuery());
        $this->assertNull($limit->getLimit());
    }

    public function testPattern(): void
    {
        $limit = new LimitClause();
        $expression = $this->getQueryConvertibleMock(NumeralType::class, "10");

        $limit->setLimit($expression);

        $this->assertSame("LIMIT 10", $limit->toQuery());
        $this->assertEquals($expression, $limit->getLimit());
    }

    public function testAcceptsNumeralType(): void
    {
        $limit = new LimitClause();
        $expression = $this->getQueryConvertibleMock(NumeralType::class, "10");

        $limit->setLimit($expression);

        $limit->toQuery();
        $this->assertEquals($expression, $limit->getLimit());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $limit = new LimitClause();
        $expression = $this->getQueryConvertibleMock(AnyType::class, "10");

        $this->expectException(TypeError::class);

        $limit->setLimit($expression);

        $limit->toQuery();
    }
}
