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

use LogicException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Functions\Date;
use WikibaseSolutions\CypherDSL\Functions\DateTime;
use WikibaseSolutions\CypherDSL\Functions\LocalDateTime;
use WikibaseSolutions\CypherDSL\Functions\LocalTime;
use WikibaseSolutions\CypherDSL\Functions\Point;
use WikibaseSolutions\CypherDSL\Functions\Time;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Literals\Literal
 */
class LiteralTest extends TestCase
{
    use TestHelper;

    public function testLiteralString(): void
    {
        $string = Literal::literal('Testing is a virtue!');

        $this->assertInstanceOf(StringLiteral::class, $string);
    }

    public function testLiteralBoolean(): void
    {
        $boolean = Literal::literal(true);

        $this->assertInstanceOf(Boolean::class, $boolean);
    }

    public function testLiteralInteger(): void
    {
        $integer = Literal::literal(1);

        $this->assertInstanceOf(Decimal::class, $integer);
    }

    public function testLiteralFloat(): void
    {
        $float = Literal::literal(1.0);

        $this->assertInstanceOf(Decimal::class, $float);
    }

    public function testStringable(): void
    {
        $stringable = Literal::literal(new class () {
            public function __toString()
            {
                return 'Testing is a virtue!';
            }
        });

        $this->assertInstanceOf(StringLiteral::class, $stringable);
    }

    public function testBoolean(): void
    {
        $boolean = Literal::boolean(true);

        $this->assertInstanceOf(Boolean::class, $boolean);

        $boolean = Literal::boolean(false);

        $this->assertInstanceOf(Boolean::class, $boolean);
    }

    public function testString(): void
    {
        $string = Literal::string('Testing is a virtue!');

        $this->assertInstanceOf(StringLiteral::class, $string);
    }

    public function testDecimal(): void
    {
        $decimal = Literal::decimal(1);

        $this->assertInstanceOf(Decimal::class, $decimal);

        $decimal = Literal::decimal(1.0);

        $this->assertInstanceOf(Decimal::class, $decimal);
    }

    public function testPoint2d(): void
    {
        $point = Literal::point2d(1, 2);

        $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "crs" => new StringLiteral("cartesian")])), $point);

        $point = Literal::point2d(
            new Decimal(1),
            new Decimal(2)
        );

        $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "crs" => new StringLiteral("cartesian")])), $point);
    }

    public function testPoint3d(): void
    {
        $point = Literal::point3d(1, 2, 3);

        $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "z" => new Decimal(3), "crs" => new StringLiteral("cartesian-3D")])), $point);

        $point = Literal::point3d(
            new Decimal(1),
            new Decimal(2),
            new Decimal(3)
        );

        $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "z" => new Decimal(3), "crs" => new StringLiteral("cartesian-3D")])), $point);
    }

    public function testPoint2dWGS84(): void
    {
        $point = Literal::point2dWGS84(1, 2);

        $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "crs" => new StringLiteral("WGS-84")])), $point);

        $point = Literal::point2dWGS84(
            new Decimal(1),
            new Decimal(2)
        );

        $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "crs" => new StringLiteral("WGS-84")])), $point);
    }

    public function testPoint3dWGS84(): void
    {
        $point = Literal::point3dWGS84(1, 2, 3);

        $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "height" => new Decimal(3), "crs" => new StringLiteral("WGS-84-3D")])), $point);

        $point = Literal::point3dWGS84(
            new Decimal(1),
            new Decimal(2),
            new Decimal(3)
        );

        $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "height" => new Decimal(3), "crs" => new StringLiteral("WGS-84-3D")])), $point);
    }

    public function testDate(): void
    {
        $date = Literal::date();

        $this->assertEquals(new Date(), $date);
    }

    public function testDateTimezone(): void
    {
        $date = Literal::date("Europe/Amsterdam");

        $this->assertEquals(new Date(new PropertyMap(["timezone" => new StringLiteral("Europe/Amsterdam")])), $date);

        $date = Literal::date(new StringLiteral("Europe/Amsterdam"));

        $this->assertEquals(new Date(new PropertyMap(["timezone" => new StringLiteral("Europe/Amsterdam")])), $date);
    }

    /**
     * @dataProvider provideDateYMDData
     * @param $year
     * @param $month
     * @param $day
     * @param $expected
     */
    public function testDateYMD($year, $month, $day, $expected): void
    {
        $date = Literal::dateYMD($year, $month, $day);

        $this->assertEquals($expected, $date);
    }

    public function testDateYMDMissingMonth(): void
    {
        $this->expectException(LogicException::class);

        $date = Literal::dateYMD(2000, null, 17);

        $date->toQuery();
    }

    /**
     * @dataProvider provideDateYWDData
     * @param $year
     * @param $week
     * @param $weekday
     * @param $expected
     */
    public function testDateYWD($year, $week, $weekday, $expected): void
    {
        $date = Literal::dateYWD($year, $week, $weekday);

        $this->assertEquals($expected, $date);
    }

    public function testDateYWDMissingWeek(): void
    {
        $this->expectException(LogicException::class);

        $date = Literal::dateYWD(2000, null, 17);

        $date->toQuery();
    }

    public function testDateString(): void
    {
        $date = Literal::dateString('2000-17-12');

        $this->assertEquals(new Date(new StringLiteral('2000-17-12')), $date);
    }

    public function testDateTimeWithoutTimeZone(): void
    {
        $datetime = Literal::dateTime();

        $this->assertEquals(new DateTime(), $datetime);
    }

    public function testDateTimeWithTimeZone(): void
    {
        $datetime = Literal::dateTime("America/Los Angeles");

        $this->assertEquals(new DateTime(new PropertyMap(["timezone" => new StringLiteral("America/Los Angeles")])), $datetime);
    }

    /**
     * @dataProvider provideDatetimeYMDData
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $timezone
     * @param $expected
     */
    public function testDatetimeYMD($year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected): void
    {
        $datetime = Literal::dateTimeYMD($year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

        $this->assertEquals($expected, $datetime);
    }

    public function testDatetimeYMDMissingMonth(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::dateTimeYMD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideDatetimeYWDData
     * @param $year
     * @param $week
     * @param $dayOfWeek
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $timezone
     * @param $expected
     */
    public function testDatetimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected): void
    {
        $datetime = Literal::datetimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

        $this->assertEquals($expected, $datetime);
    }

    public function testDatetimeYWDMissingWeek(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::dateTimeYWD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideDatetimeYQDData
     * @param $year
     * @param $quarter
     * @param $dayOfQuarter
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $timezone
     * @param $expected
     */
    public function testDatetimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected): void
    {
        $datetime = Literal::datetimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

        $this->assertEquals($expected, $datetime);
    }

    public function testDatetimeYQDMissingQuarter(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::dateTimeYQD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideDatetimeYQData
     * @param $year
     * @param $ordinalDay
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $timezone
     * @param $expected
     */
    public function testDatetimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected): void
    {
        $datetime = Literal::datetimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

        $this->assertEquals($expected, $datetime);
    }

    public function testDatetimeYDMissingOrdinalDay(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::dateTimeYD(2000, null, 17);

        $datetime->toQuery();
    }

    public function testDatetimeString(): void
    {
        $datetime = Literal::datetimeString("2015-07-21T21:40:32.142+01:00");

        $this->assertEquals(new DateTime(new StringLiteral("2015-07-21T21:40:32.142+01:00")), $datetime);
    }

    public function testLocalDateTimeWithoutTimezone(): void
    {
        $localDateTime = Literal::localDatetime();

        $this->assertEquals(new LocalDateTime(), $localDateTime);
    }

    public function testLocalDateTimeWithTimezone(): void
    {
        $localDateTime = Literal::localDatetime("America/Los Angeles");

        $this->assertEquals(new LocalDateTime(new PropertyMap(["timezone" => new StringLiteral("America/Los Angeles")])), $localDateTime);
    }

    /**
     * @dataProvider provideLocalDatetimeYMDData
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testLocalDateTimeYMD($year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $localDatetime = Literal::localDatetimeYMD($year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYMDMissingMonth(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDatetimeYMD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideLocalDatetimeYWDData
     * @param $year
     * @param $week
     * @param $dayOfWeek
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testLocalDateTimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $localDatetime = Literal::localDatetimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYWDMissingWeek(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDatetimeYWD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideLocalDatetimeYQDData
     * @param $year
     * @param $quarter
     * @param $dayOfQuarter
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testLocalDatetimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $localDatetime = Literal::localDatetimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYQDMissingQuarter(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDatetimeYQD(2000, null, 17);

        $datetime->toQuery();
    }

    /**
     * @dataProvider provideLocalDatetimeYQData
     * @param $year
     * @param $ordinalDay
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testLocalDatetimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $localDatetime = Literal::localDatetimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYDMissingOrdinalDay(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDatetimeYD(2000, null, 17);

        $datetime->toQuery();
    }

    public function testLocalDatetimeString(): void
    {
        $localDatetime = Literal::localDatetimeString("2015-W30-2T214032.142");

        $this->assertEquals(new LocalDateTime(new StringLiteral("2015-W30-2T214032.142")), $localDatetime);
    }

    public function testLocalTimeCurrentWithoutTimezone(): void
    {
        $localTime = Literal::localTimeCurrent();
        $this->assertEquals(new LocalTime(), $localTime);
    }

    public function testLocalTimeCurrentWithTimezone(): void
    {
        $localTime = Literal::localTimeCurrent("America/Los Angeles");
        $this->assertEquals(new LocalTime(new PropertyMap(["timezone" => new StringLiteral("America/Los Angeles")])), $localTime);
    }

    /**
     * @dataProvider provideLocalTimeData
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testLocalTime($hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $localTime = Literal::localTime($hour, $minute, $second, $millisecond, $microsecond, $nanosecond);
        $this->assertEquals($localTime, $expected);
    }

    public function testLocalTimeMissingMinute(): void
    {
        $this->expectException(LogicException::class);

        $localTime = Literal::localTime(9, null, 17);

        $localTime->toQuery();
    }

    public function testLocalTimeString(): void
    {
        $localTime = Literal::localTimeString("21:40:32.142");
        $this->assertEquals(new LocalTime(new StringLiteral("21:40:32.142")), $localTime);
    }

    public function testTimeCurrentWithoutTimezone(): void
    {
        $time = Literal::time();
        $this->assertEquals($time, new Time());
    }

    public function testTimeCurrentWithTimezone(): void
    {
        $time = Literal::time("America/Los Angeles");
        $this->assertEquals($time, new Time(new PropertyMap(["timezone" => new StringLiteral("America/Los Angeles")])));
    }

    /**
     * @dataProvider provideTimeData
     * @param $hour
     * @param $minute
     * @param $second
     * @param $millisecond
     * @param $microsecond
     * @param $nanosecond
     * @param $expected
     */
    public function testTime($hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected): void
    {
        $time = Literal::timeHMS($hour, $minute, $second, $millisecond, $microsecond, $nanosecond);
        $this->assertEquals($time, $expected);
    }

    public function testTimeMissingMinute(): void
    {
        $this->expectException(LogicException::class);

        $time = Literal::timeHMS(9, null, 17);

        $time->toQuery();
    }

    public function testTimeString(): void
    {
        $time = Literal::timeString("21:40:32.142+0100");
        $this->assertEquals($time, new Time(new StringLiteral("21:40:32.142+0100")));
    }

    public function provideDateYMDData(): array
    {
        return [
            [2000, null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [2000, 12, 17, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(17)]))],
            [new Decimal(2000), null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(17), new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(17)]))],

        ];
    }

    public function provideDateYWDData(): array
    {
        return [
            [2000, null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12)]))],
            [2000, 12, 17, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12), "dayOfWeek" => new Decimal(17)]))],
            [new Decimal(2000), null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(17), new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12), "dayOfWeek" => new Decimal(17)]))],

        ];
    }

    public function provideDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [2000, 12, 15, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15)]))],
            [2000, 12, 15, 8, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8)]))],
            [2000, 12, 15, 8, 25, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new StringLiteral("America/Los Angeles"), new DateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 9, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9)]))],
            [2000, 9, 4, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4)]))],
            [2000, 9, 4, 8, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8)]))],
            [2000, 9, 4, 8, 25, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(9), null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new StringLiteral("America/Los Angeles"), new DateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3)]))],
            [2000, 3, 4, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4)]))],
            [2000, 3, 4, 8, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8)]))],
            [2000, 3, 4, 8, 25, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(3), null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new StringLiteral("America/Los Angeles"), new DateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3)]))],
            [2000, 3, 8, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8)]))],
            [2000, 3, 8, 25, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 3, 8, 25, 44, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 3, 8, 25, 44, 18, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 3, 8, 25, 44, 18, 6, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(3), null, null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), null, null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), null, null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), null, new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new StringLiteral("America/Los Angeles"), new DateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31), "timezone" => new StringLiteral("America/Los Angeles")]))],
        ];
    }

    public function provideLocalDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [2000, 12, 15, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15)]))],
            [2000, 12, 15, 8, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8)]))],
            [2000, 12, 15, 8, 25, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null,new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(15), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(15), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
        ];
    }

    public function provideLocalDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 9, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9)]))],
            [2000, 9, 4, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4)]))],
            [2000, 9, 4, 8, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8)]))],
            [2000, 9, 4, 8, 25, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(9), null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(9), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(9), "dayOfWeek" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
        ];
    }


    public function provideLocalDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3)]))],
            [2000, 3, 4, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4)]))],
            [2000, 3, 4, 8, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8)]))],
            [2000, 3, 4, 8, 25, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(3), null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(4), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "quarter" => new Decimal(3), "dayOfQuarter" => new Decimal(4), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
        ];
    }

    public function provideLocalDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 3, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3)]))],
            [2000, 3, 8, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8)]))],
            [2000, 3, 8, 25, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [2000, 3, 8, 25, 44, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [2000, 3, 8, 25, 44, 18, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [2000, 3, 8, 25, 44, 18, 6, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],

            // types
            [new Decimal(2000), null, null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(3), null, null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), null, null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), null, null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), null, null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), null, null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), null, new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6)]))],
            [new Decimal(2000), new Decimal(3), new Decimal(8), new Decimal(25), new Decimal(44), new Decimal(18), new Decimal(6), new Decimal(31), new LocalDateTime(new PropertyMap(["year" => new Decimal(2000), "ordinalDay" => new Decimal(3), "hour" => new Decimal(8), "minute" => new Decimal(25), "second" => new Decimal(44), "millisecond" => new Decimal(18), "microsecond" => new Decimal(6), "nanosecond" => new Decimal(31)]))],
        ];
    }

    public function provideLocalTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11)]))],
            [11, 23, null, null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23)]))],
            [11, 23, 2, null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2)]))],
            [11, 23, 2, 54, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54)]))],
            [11, 23, 2, 54, 8, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8)]))],
            [11, 23, 2, 54, 8, 29, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8), "nanosecond" => new Decimal(29)]))],

            // types
            [new Decimal(11), null, null, null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11)]))],
            [new Decimal(11), new Decimal(23), null, null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), null, null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), null, null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), new Decimal(8), null, new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), new Decimal(8), new Decimal(29), new LocalTime(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8), "nanosecond" => new Decimal(29)]))],
        ];
    }

    public function provideTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11)]))],
            [11, 23, null, null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23)]))],
            [11, 23, 2, null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2)]))],
            [11, 23, 2, 54, null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54)]))],
            [11, 23, 2, 54, 8, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8)]))],
            [11, 23, 2, 54, 8, 29, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8), "nanosecond" => new Decimal(29)]))],

            // types
            [new Decimal(11), null, null, null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11)]))],
            [new Decimal(11), new Decimal(23), null, null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), null, null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), null, null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), new Decimal(8), null, new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8)]))],
            [new Decimal(11), new Decimal(23), new Decimal(2), new Decimal(54), new Decimal(8), new Decimal(29), new Time(new PropertyMap(["hour" => new Decimal(11), "minute" => new Decimal(23), "second" => new Decimal(2), "millisecond" => new Decimal(54), "microsecond" => new Decimal(8), "nanosecond" => new Decimal(29)]))],
        ];
    }
}
