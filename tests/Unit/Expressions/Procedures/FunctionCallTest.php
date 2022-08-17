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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\All;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Any;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Single;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure
 */
class FunctionCallTest extends TestCase
{
    public function testRaw()
    {
        $raw = Procedure::raw("foo", []);

        $this->assertInstanceOf(Raw::class, $raw);
    }

    public function testAll()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $all = Procedure::all($variable, $list, $predicate);

        $this->assertInstanceOf(All::class, $all);
    }

    public function testAny()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $any = Procedure::any($variable, $list, $predicate);

        $this->assertInstanceOf(Any::class, $any);
    }

    public function testExists()
    {
        $expression = $this->createMock(AnyType::class);

        $exists = Procedure::exists($expression);

        $this->assertInstanceOf(Exists::class, $exists);
    }

    public function testIsEmpty()
    {
        $list = new List_;

        $isEmpty = Procedure::isEmpty($list);

        $this->assertInstanceOf(IsEmpty::class, $isEmpty);
    }

    public function testNone()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $none = Procedure::none($variable, $list, $predicate);

        $this->assertInstanceOf(None::class, $none);
    }

    public function testSingle()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $single = Procedure::single($variable, $list, $predicate);

        $this->assertInstanceOf(Single::class, $single);
    }

    public function testPoint()
    {
        $map = new Map([]);

        $point = Procedure::point($map);

        $this->assertInstanceOf(Point::class, $point);
    }

    public function testDate()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::date($value);

        $this->assertInstanceOf(Date::class, $date);

        $date = Procedure::date();

        $this->assertInstanceOf(Date::class, $date);
    }

    public function testDateTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::datetime($value);

        $this->assertInstanceOf(DateTime::class, $date);

        $date = Procedure::datetime();

        $this->assertInstanceOf(DateTime::class, $date);
    }

    public function testLocalDateTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::localdatetime($value);

        $this->assertInstanceOf(LocalDateTime::class, $date);

        $date = Procedure::localdatetime();

        $this->assertInstanceOf(LocalDateTime::class, $date);
    }

    public function testLocalTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::localtime($value);

        $this->assertInstanceOf(LocalTime::class, $date);

        $date = Procedure::localtime();

        $this->assertInstanceOf(LocalTime::class, $date);
    }

    public function testTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::time($value);

        $this->assertInstanceOf(Time::class, $date);

        $date = Procedure::time();

        $this->assertInstanceOf(Time::class, $date);
    }
}
