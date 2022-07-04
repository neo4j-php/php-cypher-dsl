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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Inequality;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Inequality
 */
class InequalityTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $inequality = new Inequality($this->getQueryConvertibleMock(PropertyType::class, "a"), $this->getQueryConvertibleMock(PropertyType::class, "b"));

        $this->assertSame("(a <> b)", $inequality->toQuery());

        $inequality = new Inequality($inequality, $inequality);

        $this->assertSame("((a <> b) <> (a <> b))", $inequality->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $inequality = new Inequality($this->getQueryConvertibleMock(PropertyType::class, "a"), $this->getQueryConvertibleMock(PropertyType::class, "b"), false);

        $this->assertSame("a <> b", $inequality->toQuery());

        $inequality = new Inequality($inequality, $inequality);

        $this->assertSame("(a <> b <> a <> b)", $inequality->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $inequality = new Inequality($this->getQueryConvertibleMock(AnyType::class, "a"), $this->getQueryConvertibleMock(AnyType::class, "b"));

        $inequality->toQuery();
    }
}
