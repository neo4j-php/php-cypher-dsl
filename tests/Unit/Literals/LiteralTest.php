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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Literals;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;

/**
 * @covers \WikibaseSolutions\CypherDSL\Decimal
 */
class LiteralTest extends TestCase
{
    public function testInt(): void
    {
        $literal = Literal::fromLiteral(1);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1', $literal->toQuery());
    }

    public function testFloat(): void
    {
        $literal = Literal::fromLiteral(1.2);
        self::assertInstanceOf(Decimal::class, $literal);
        self::assertEquals('1.2', $literal->toQuery());
    }

    public function testString(): void
    {
        $literal = Literal::fromLiteral('abc');
        self::assertInstanceOf(StringLiteral::class, $literal);
        self::assertEquals("'abc'", $literal->toQuery());
    }

    public function testStringAble(): void
    {
        $literal = Literal::fromLiteral(new class () {
            public function __toString(): string
            {
                return 'stringable abc';
            }
        });
        self::assertInstanceOf(StringLiteral::class, $literal);
        self::assertEquals("'stringable abc'", $literal->toQuery());
    }

    public function testBool(): void
    {
        $literal = Literal::fromLiteral(true);
        self::assertInstanceOf(Boolean::class, $literal);
        self::assertEquals("true", $literal->toQuery());
    }

    public function testTypeError(): void
    {
        $literal = Literal::fromLiteral(true);
        $this->expectException(TypeError::class);
        Literal::fromLiteral($literal);
    }
}