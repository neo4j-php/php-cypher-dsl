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
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WhereClause
 */
class WhereClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $where = new WhereClause();

        $this->assertSame("", $where->toQuery());
        $this->assertNull($where->getExpression());
    }

    public function testExpression(): void
    {
        $where = new WhereClause();
        $expression = $this->createMock(BooleanType::class);
        $expression->method('toQuery')->willReturn('(a)');

        $where->addExpression($expression);

        $this->assertSame("WHERE (a)", $where->toQuery());
        $this->assertEquals($expression, $where->getExpression());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testDoesNotAcceptAnyType(): void
    {
        $where = new WhereClause();
        $expression = $this->createMock(AnyType::class);
        $this->expectException(TypeError::class);

        $where->addExpression($expression);
    }
}
