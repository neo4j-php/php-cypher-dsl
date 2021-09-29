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
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RemoveClause
 */
class RemoveClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
    }

    public function testSingleExpression()
    {
        $remove = new RemoveClause();
        $expression = $this->getExpressionMock("(a)", $this);

        $remove->addExpression($expression);

        $this->assertSame("REMOVE (a)", $remove->toQuery());
    }

    public function testMultipleExpressions()
    {
        $remove = new RemoveClause();

        $a = $this->getExpressionMock("(a)", $this);
        $b = $this->getExpressionMock("(b)", $this);

        $remove->addExpression($a);
        $remove->addExpression($b);

        $this->assertSame("REMOVE (a), (b)", $remove->toQuery());
    }
}