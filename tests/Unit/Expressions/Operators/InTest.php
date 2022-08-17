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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\In
 */
class InTest extends TestCase
{
    public function testToQuery(): void
    {
        $in = new In(new Property(new Variable('v'), "a"), new Variable('b'));

        $this->assertSame("(v.a IN b)", $in->toQuery());

        $in = new In($in, new List_([new Boolean(true), new Boolean(false)]));

        $this->assertSame("((v.a IN b) IN [true, false])", $in->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $in = new In(new Property(new Variable('v'), "a"), new Variable('b'), false);

        $this->assertSame("v.a IN b", $in->toQuery());

        $in = new In($in, new List_([new Boolean(true), new Boolean(false)]));

        $this->assertSame("(v.a IN b IN [true, false])", $in->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $inequality = new In($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $inequality->toQuery();
    }
}
