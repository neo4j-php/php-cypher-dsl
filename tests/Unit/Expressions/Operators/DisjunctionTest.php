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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction
 */
class DisjunctionTest extends TestCase
{

    public function testToQuery(): void
    {
        $or = new Disjunction(new Boolean(true), new Boolean(false));

        $this->assertSame("(true OR false)", $or->toQuery());

        $or = new Disjunction($or, $or);

        $this->assertSame("((true OR false) OR (true OR false))", $or->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $or = new Disjunction(new Boolean(true), new Boolean(false), false);

        $this->assertSame("true OR false", $or->toQuery());

        $or = new Disjunction($or, $or);

        $this->assertSame("(true OR false OR true OR false)", $or->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $or = new Disjunction($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $or->toQuery();
    }
}