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
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Property
 */
class PropertyTest extends TestCase
{
    use TestHelper;

    /**
     * @dataProvider provideToQueryData
     * @param        Variable $variable
     * @param        string   $property
     * @param        string   $expected
     */
    public function testToQuery(Variable $variable, string $property, string $expected)
    {
        $property = new Property($variable, $property);

        $this->assertSame($expected, $property->toQuery());
    }

    public function provideToQueryData(): array
    {
        return [
        [$this->getVariableMock("a", $this), "a", "a.a"],
        [$this->getVariableMock("a", $this), "b", "a.b"],
        [$this->getVariableMock("b", $this), "a", "b.a"],
        [$this->getVariableMock("a", $this), ":", "a.`:`"],
        [$this->getVariableMock("b", $this), ":", "b.`:`"]
        ];
    }
}