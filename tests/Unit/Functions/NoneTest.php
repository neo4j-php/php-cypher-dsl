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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Functions\None;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Functions\None
 */
class NoneTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $variable = $this->getQueryConvertibleMock(Variable::class, "variable");
        $list = $this->getQueryConvertibleMock(ListType::class, "list");
        $predicate = $this->getQueryConvertibleMock(AnyType::class, "predicate");

        $all = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN list WHERE predicate)", $all->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable()
    {
        $variable = $this->getQueryConvertibleMock(AnyType::class, "variable");
        $list = $this->getQueryConvertibleMock(ListType::class, "list");
        $predicate = $this->getQueryConvertibleMock(AnyType::class, "predicate");

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsList()
    {
        $variable = $this->getQueryConvertibleMock(Variable::class, "variable");
        $list = $this->getQueryConvertibleMock(AnyType::class, "list");
        $predicate = $this->getQueryConvertibleMock(AnyType::class, "predicate");

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }
}
