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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Parameter
 */
class ParameterTest extends TestCase
{
    /**
     * @dataProvider provideToQueryData
     * @param string $parameter
     * @param string $expected
     */
    public function testToQuery(string $parameter, string $expected): void
    {
        $parameter = new Parameter($parameter);

        $this->assertSame($expected, $parameter->toQuery());
        $this->assertSame(str_replace('$', '', $expected), $parameter->getParameter());
    }

    /**
     * @dataProvider provideThrowsExceptionOnInvalidData
     * @param string $parameter
     */
    public function testThrowsExceptionOnInvalid(string $parameter): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Parameter($parameter);
    }

    public function provideToQueryData(): array
    {
        return [
            ["a", '$a'],
            ["b", '$b'],
            ["foo_bar", '$foo_bar'],
            ["A", '$A'],
        ];
    }

    public function provideThrowsExceptionOnInvalidData(): array
    {
        return [
            [""],
            ["@"],
            ["!"],
            ["-"],
            [''],
        ];
    }
}
