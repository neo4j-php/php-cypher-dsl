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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * Dummy classes
 */
class ErrorHelperDummyA {};
class ErrorHelperDummyB {};
class ErrorHelperDummyExtendsA extends ErrorHelperDummyA {};
class ErrorHelperDummyExtendsB extends ErrorHelperDummyB {};

/**
 * Tester/Mock class
 */
class ErrorImpl {
    use ErrorTrait;

    /**
     * Overcome private method problems
     */
    public function call($funcName, $args) {
        return call_user_func_array([self::class, $funcName], $args);
    }
}

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\ErrorTrait
 */
class ErrorTraitTest extends TestCase
{
    protected ErrorImpl $errorImpl;

    public function setUp() : void {
        $this->errorImpl = new ErrorImpl();
    }

    /**
     * @doesNotPerformAssertions
     * @dataProvider CorrectAssertionsProvider
     */
    public function testAssertClass($classNames, $userInput) {
        $this->errorImpl->call('assertClass', ['foo', $classNames, $userInput]);
    }

    /**
     * @dataProvider failingAssertionsProvider
     */
    public function testAssertClassFailure($classNames, $userInput) {
        $this->expectException(TypeError::class);
        $this->errorImpl->call('assertClass', ['foo', $classNames, $userInput]);
    }

    public function correctAssertionsProvider() {
        return [
            [ErrorHelperDummyA::class, new ErrorHelperDummyA()],
            [ErrorHelperDummyA::class, new ErrorHelperDummyExtendsA()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyB::class], new ErrorHelperDummyB()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyB::class], new ErrorHelperDummyExtendsB()],
        ];
    }

    public function failingAssertionsProvider() {
        return [
            [ErrorHelperDummyA::class, new ErrorHelperDummyB()],
            [ErrorHelperDummyExtendsA::class, new ErrorHelperDummyA()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyExtendsB::class], new ErrorHelperDummyB()],
        ];
    }

    public function testGetTypeErrorText() {
        $this->assertEquals(
            '$foo should be a WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyA object, integer "5" given.',
            $this->errorImpl->call('getTypeErrorText', ['foo', [ErrorHelperDummyA::class], 5])
        );
        $this->assertEquals(
            '$foo should be a ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyA or ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyB object, integer "5" given.',
            $this->errorImpl->call('getTypeErrorText', ['foo', [ErrorHelperDummyA::class, ErrorHelperDummyB::class], 5])
        );
    }

    /**
     * @dataProvider getUserInputInfoProvider
     */
    public function testGetUserInputInfo($expected, $input) {
        $this->assertEquals(
            $expected,
            $this->errorImpl->call('getUserInputInfo', [$input])
        );
    }

    public function getUserInputInfoProvider() {
        return [
            [ 'string "foo"',             'foo'          ],
            [ 'integer "5"',              5              ],
            [ 'double "3.14"',            3.14           ],
            [ 'boolean "1"',              true           ],
            [ 'array',                    ['foo', 'bar'] ],
            [ 'anonymous class instance', new class(){}  ],
            [ 'WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyA', new ErrorHelperDummyA()]
        ];
    }
}