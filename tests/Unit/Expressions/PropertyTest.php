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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Property
 */
class PropertyTest extends TestCase
{
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
        [$this->getVariableMock("a"), "a", "a.a"],
        [$this->getVariableMock("a"), "b", "a.b"],
        [$this->getVariableMock("b"), "a", "b.a"],
        [$this->getVariableMock("a"), ":", "a.`:`"],
        [$this->getVariableMock("b"), ":", "b.`:`"]
        ];
    }

    /**
     * Returns a mock of the Variable class that returns the given string when toQuery() is called.
     *
     * @param  string $variable
     * @return Variable|MockObject
     */
    private function getVariableMock(string $variable): Variable
    {
        $mock = $this->getMockBuilder(Variable::class)->disableOriginalConstructor()->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}