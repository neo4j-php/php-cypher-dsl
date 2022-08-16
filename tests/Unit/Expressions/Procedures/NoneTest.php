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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\None
 */
class NoneTest extends TestCase
{

    public function testToQuery()
    {
        $variable = new Variable("variable");
        $list = new List_([new String_('foo'), new String_('bar')]);
        $predicate = $this->createMock(AnyType::class);
        $predicate->method('toQuery')->willReturn('predicate');

        $all = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN ['foo', 'bar'] WHERE predicate)", $all->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable()
    {
        $variable = $this->createMock(AnyType::class);
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsList()
    {
        $variable = new Variable("variable");
        $list = $this->createMock(AnyType::class);
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }
}
