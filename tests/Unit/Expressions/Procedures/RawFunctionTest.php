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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw
 */
class RawFunctionTest extends TestCase
{
    public function testToQuery()
    {
        $a = new Variable('a');
        $b = new Variable('b');
        $c = new Variable('c');

        $raw = new Raw("foobar", [$a, $b, $c]);

        $this->assertSame("foobar(a, b, c)", $raw->toQuery());
    }

    public function testRequiresAnyTypeParameters()
    {
        $a = new class () {};

        $this->expectException(TypeError::class);

        new Raw('foobar', [$a]);
    }
}
