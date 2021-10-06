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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\GreaterThanOrEqual
 */
class GreaterThanOrEqualTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $greaterThanOrEqual = new GreaterThanOrEqual($this->getExpressionMock("a", $this), $this->getExpressionMock("b", $this));

        $this->assertSame("(a >= b)", $greaterThanOrEqual->toQuery());

        $greaterThanOrEqual = new GreaterThanOrEqual($greaterThanOrEqual, $greaterThanOrEqual);

        $this->assertSame("((a >= b) >= (a >= b))", $greaterThanOrEqual->toQuery());
    }
}