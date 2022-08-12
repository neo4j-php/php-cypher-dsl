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
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Expressions\EndsWith;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith
 */
class EndsWithTest extends TestCase
{
    use TestHelper;

    public function testToQuery(): void
    {
        $endsWith = new EndsWith($this->getQueryConvertibleMock(StringType::class, "a"), $this->getQueryConvertibleMock(StringType::class, "b"));

        $this->assertSame("(a ENDS WITH b)", $endsWith->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $endsWith = new EndsWith($this->getQueryConvertibleMock(StringType::class, "a"), $this->getQueryConvertibleMock(StringType::class, "b"), false);

        $this->assertSame("a ENDS WITH b", $endsWith->toQuery());
    }

    public function testCannotBeNested(): void
    {
        $endsWith = new EndsWith($this->getQueryConvertibleMock(StringType::class, "a"), $this->getQueryConvertibleMock(StringType::class, "b"));

        $this->expectException(TypeError::class);

        $endsWith = new EndsWith($endsWith, $endsWith);

        $endsWith->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $endsWith = new EndsWith($this->getQueryConvertibleMock(AnyType::class, "a"), $this->getQueryConvertibleMock(AnyType::class, "b"));

        $endsWith->toQuery();
    }
}
