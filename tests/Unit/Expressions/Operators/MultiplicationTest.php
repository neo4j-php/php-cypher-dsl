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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication
 */
class MultiplicationTest extends TestCase
{

    public function testToQuery(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15));

        $this->assertSame("(10 * 15)", $multiplication->toQuery());

        $multiplication = new Multiplication($multiplication, $multiplication);

        $this->assertSame("((10 * 15) * (10 * 15))", $multiplication->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15), false);

        $this->assertSame("10 * 15", $multiplication->toQuery());

        $multiplication = new Multiplication($multiplication, $multiplication);

        $this->assertSame("(10 * 15 * 10 * 15)", $multiplication->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $multiplication = new Multiplication($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $multiplication->toQuery();
    }
}
