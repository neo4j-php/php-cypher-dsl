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

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\ExpressionList;
use WikibaseSolutions\CypherDSL\In;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\In
 */
class InTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $inequality = new In($this->getQueryConvertibleMock(PropertyType::class, "a"), $this->getQueryConvertibleMock(ListType::class, "b"));

        $this->assertSame("(a IN b)", $inequality->toQuery());

        $inequality = new In($inequality, $this->getQueryConvertibleMock(ListType::class, "c"));

        $this->assertSame("((a IN b) IN c)", $inequality->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $inequality = new In($this->getQueryConvertibleMock(PropertyType::class, "a"), $this->getQueryConvertibleMock(ListType::class, "b"), false);

        $this->assertSame("a IN b", $inequality->toQuery());

        $inequality = new In($inequality, $this->getQueryConvertibleMock(ListType::class, "c"));

        $this->assertSame("(a IN b IN c)", $inequality->toQuery());
    }

    public function testInExpressionList(): void
    {
        $inequality = new In($this->getQueryConvertibleMock(PropertyType::class, "a"), new ExpressionList([new StringLiteral('a'), new StringLiteral('b')]));

        $this->assertSame("(a IN ['a', 'b'])", $inequality->toQuery());

        $inequality = new In($inequality, $this->getQueryConvertibleMock(ListType::class, "c"));

        $this->assertSame("((a IN ['a', 'b']) IN c)", $inequality->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $inequality = new In($this->getQueryConvertibleMock(AnyType::class, "a"), $this->getQueryConvertibleMock(AnyType::class, "b"));

        $inequality->toQuery();
    }
}
