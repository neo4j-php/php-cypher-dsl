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
use WikibaseSolutions\CypherDSL\Expressions\EndsWith;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\EndsWith
 */
class EndsWithTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $endsWith = new EndsWith($this->getExpressionMock("a", $this), $this->getExpressionMock("b", $this));

        $this->assertSame("(a ENDS WITH b)", $endsWith->toQuery());

        $endsWith = new EndsWith($endsWith, $endsWith);

        $this->assertSame("((a ENDS WITH b) ENDS WITH (a ENDS WITH b))", $endsWith->toQuery());
    }
}