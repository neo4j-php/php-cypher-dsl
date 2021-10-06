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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

trait TestHelper
{
    /**
     * @param  string   $variable
     * @param  TestCase $testCase
     * @return Pattern|MockObject
     */
    public function getPatternMock(string $variable, TestCase $testCase): Pattern
    {
        $mock = $testCase->getMockBuilder(Pattern::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

    /**
     * @param  string   $variable
     * @param  TestCase $testCase
     * @return Property|MockObject
     */
    public function getPropertyMock(string $variable, TestCase $testCase): Property
    {
        $mock = $testCase->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

    /**
     * @param  string   $variable
     * @param  TestCase $testCase
     * @return Expression|MockObject
     */
    public function getExpressionMock(string $variable, TestCase $testCase): Expression
    {
        $mock = $testCase->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

    /**
     * @param  string   $variable
     * @param  TestCase $testCase
     * @return Clause|MockObject
     */
    public function getClauseMock(string $variable, TestCase $testCase): Clause
    {
        $mock = $testCase->getMockBuilder(Clause::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

    /**
     * @param  string   $variable
     * @param  TestCase $testCase
     * @return Variable|MockObject
     */
    public function getVariableMock(string $variable, TestCase $testCase): Variable
    {
        $mock = $testCase->getMockBuilder(Variable::class)->disableOriginalConstructor()->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}