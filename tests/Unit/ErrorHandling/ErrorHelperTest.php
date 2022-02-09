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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\ErrorHandling;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\ErrorHandling\ErrorHelper;

/**
 * Dummy classes
 */
class ErrorHelperDummyA {};
class ErrorHelperDummyB {};
class ErrorHelperDummyExtendsA extends ErrorHelperDummyA {};
class ErrorHelperDummyExtendsB extends ErrorHelperDummyB {};

/**
 * @covers \WikibaseSolutions\CypherDSL\ErrorHandling\ErrorHelper
 */
class ErrorHelperTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     * @dataProvider CorrectAssertionsProvider
     */
    public function testAssertClass($classNames, $userInput) {
        ErrorHelper::assertClass('foo', $classNames, $userInput);
    }

    /**
     * @dataProvider failingAssertionsProvider
     */
    public function testAssertClassFailure($classNames, $userInput) {
        $this->expectException(TypeError::class);
        ErrorHelper::assertClass('foo', $classNames, $userInput);
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
            '$foo should be a WikibaseSolutions\CypherDSL\Tests\Unit\ErrorHandling\ErrorHelperDummyA object, integer "5" given.',
            ErrorHelper::getTypeErrorText('foo', [ErrorHelperDummyA::class], 5)
        );
        $this->assertEquals(
            '$foo should be a ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\ErrorHandling\ErrorHelperDummyA or ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\ErrorHandling\ErrorHelperDummyB object, integer "5" given.',
            ErrorHelper::getTypeErrorText('foo', [ErrorHelperDummyA::class, ErrorHelperDummyB::class], 5)
        );
    }

    public function testGetUserInputInfo() {
        $this->assertEquals(
            'string "foo"',
            ErrorHelper::getUserInputInfo('foo')
        );
        $this->assertEquals(
            'integer "5"',
            ErrorHelper::getUserInputInfo(5)
        );
        $this->assertEquals(
            'double "3.14"',
            ErrorHelper::getUserInputInfo(3.14)
        );
        $this->assertEquals(
            'boolean "1"',
            ErrorHelper::getUserInputInfo(true)
        );
        $this->assertEquals(
            'array',
            ErrorHelper::getUserInputInfo(['foo', 'bar'])
        );
        $this->assertEquals(
            'anonymous class instance',
            ErrorHelper::getUserInputInfo(new class(){})
        );
        $this->assertEquals(
            'WikibaseSolutions\CypherDSL\Tests\Unit\ErrorHandling\ErrorHelperDummyA',
            ErrorHelper::getUserInputInfo(new ErrorHelperDummyA())
        );
    }
}
