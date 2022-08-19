<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use JetBrains\PhpStorm\Internal\TentativeType;
use LogicException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Literal
 */
final class LiteralTest extends TestCase
{
    public function testLiteralString(): void
    {
        $string = Literal::literal('Testing is a virtue!');

        $this->assertInstanceOf(String_::class, $string);
    }

    public function testLiteralBoolean(): void
    {
        $boolean = Literal::literal(true);

        $this->assertInstanceOf(Boolean::class, $boolean);
    }

    public function testLiteralInteger(): void
    {
        $integer = Literal::literal(1);

        $this->assertInstanceOf(Integer::class, $integer);
    }

    public function testLiteralFloat(): void
    {
        $float = Literal::literal(1.0);

        $this->assertInstanceOf(Float_::class, $float);
    }

    public function testLiteralList(): void
    {
        $list = Literal::literal(['a', 'b', 'c']);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testLiteralMap(): void
    {
        $map = Literal::literal(['a' => 'b']);

        $this->assertInstanceOf(Map::class, $map);
    }

    public function testInvalidTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Literal::literal(new \stdClass());
    }

    public function testStringable(): void
    {
        $stringable = Literal::literal(new class () {
            public function __toString()
            {
                return 'Testing is a virtue!';
            }
        });

        $this->assertInstanceOf(String_::class, $stringable);
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

        $this->assertInstanceOf(String_::class, $string);
    }

    public function testDecimal(): void
    {
        $decimal = Literal::decimal(1);

        $this->assertInstanceOf(Integer::class, $decimal);

        $decimal = Literal::decimal(1.0);

        $this->assertInstanceOf(Float_::class, $decimal);
    }

    public function testInteger(): void
    {
        $integer = Literal::integer(1);

        $this->assertInstanceOf(Integer::class, $integer);
    }

    public function testFloat(): void
    {
        $float = Literal::float(1.0);

        $this->assertInstanceOf(Float_::class, $float);
    }

    public function testList(): void
    {
        $list = Literal::list(['a', 'b', 'c']);

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testListAcceptsIterable(): void
    {
        $list = Literal::list(new class implements \Iterator {
            public function current()
            {
                return 1;
            }

            public function next()
            {
                return 1;
            }

            public function key()
            {
            }

            public function valid()
            {
                static $i = 0;
                return $i++ < 10;
            }

            public function rewind()
            {
            }
        } );

        $this->assertInstanceOf(List_::class, $list);
    }

    public function testListCastsToAnyType(): void
    {
        $list = Literal::list(['a', 1, true]);

        $this->assertSame("['a', 1, true]", $list->toQuery());
    }

    public function testMapCastsToAnyType(): void
    {
        $map = Literal::map(['a' => 'a', 'b' => 1, 'c' => true]);

        $this->assertSame("{a: 'a', b: 1, c: true}", $map->toQuery());
    }

    public function testMap(): void
    {
        $map = Literal::map(['a' => 'b']);

        $this->assertInstanceOf(Map::class, $map);
    }

    public function testPoint2d(): void
    {
        $point = Literal::point2d(1, 2);

        $this->assertEquals(new Point(new Map(["x" => new Integer(1), "y" => new Integer(2), "crs" => new String_("cartesian")])), $point);

        $point = Literal::point2d(
            new Integer(1),
            new Integer(2)
        );

        $this->assertEquals(new Point(new Map(["x" => new Integer(1), "y" => new Integer(2), "crs" => new String_("cartesian")])), $point);
    }

    public function testPoint3d(): void
    {
        $point = Literal::point3d(1, 2, 3);

        $this->assertEquals(new Point(new Map(["x" => new Integer(1), "y" => new Integer(2), "z" => new Integer(3), "crs" => new String_("cartesian-3D")])), $point);

        $point = Literal::point3d(
            new Integer(1),
            new Integer(2),
            new Integer(3)
        );

        $this->assertEquals(new Point(new Map(["x" => new Integer(1), "y" => new Integer(2), "z" => new Integer(3), "crs" => new String_("cartesian-3D")])), $point);
    }

    public function testPoint2dWGS84(): void
    {
        $point = Literal::point2dWGS84(1, 2);

        $this->assertEquals(new Point(new Map(["longitude" => new Integer(1), "latitude" => new Integer(2), "crs" => new String_("WGS-84")])), $point);

        $point = Literal::point2dWGS84(
            new Integer(1),
            new Integer(2)
        );

        $this->assertEquals(new Point(new Map(["longitude" => new Integer(1), "latitude" => new Integer(2), "crs" => new String_("WGS-84")])), $point);
    }

    public function testPoint3dWGS84(): void
    {
        $point = Literal::point3dWGS84(1, 2, 3);

        $this->assertEquals(new Point(new Map(["longitude" => new Integer(1), "latitude" => new Integer(2), "height" => new Integer(3), "crs" => new String_("WGS-84-3D")])), $point);

        $point = Literal::point3dWGS84(
            new Integer(1),
            new Integer(2),
            new Integer(3)
        );

        $this->assertEquals(new Point(new Map(["longitude" => new Integer(1), "latitude" => new Integer(2), "height" => new Integer(3), "crs" => new String_("WGS-84-3D")])), $point);
    }

    public function testDate(): void
    {
        $date = Literal::date();

        $this->assertEquals(new Date(), $date);
    }

    public function testDateTimezone(): void
    {
        $date = Literal::date("Europe/Amsterdam");

        $this->assertEquals(new Date(new Map(["timezone" => new String_("Europe/Amsterdam")])), $date);

        $date = Literal::date(new String_("Europe/Amsterdam"));

        $this->assertEquals(new Date(new Map(["timezone" => new String_("Europe/Amsterdam")])), $date);
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

        $this->assertEquals(new Date(new String_('2000-17-12')), $date);
    }

    public function testDateTimeWithoutTimeZone(): void
    {
        $datetime = Literal::dateTime();

        $this->assertEquals(new DateTime(), $datetime);
    }

    public function testDateTimeWithTimeZone(): void
    {
        $datetime = Literal::dateTime("America/Los Angeles");

        $this->assertEquals(new DateTime(new Map(["timezone" => new String_("America/Los Angeles")])), $datetime);
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
        $datetime = Literal::dateTimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

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
        $datetime = Literal::dateTimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

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
        $datetime = Literal::dateTimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone);

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
        $datetime = Literal::dateTimeString("2015-07-21T21:40:32.142+01:00");

        $this->assertEquals(new DateTime(new String_("2015-07-21T21:40:32.142+01:00")), $datetime);
    }

    public function testLocalDateTimeWithoutTimezone(): void
    {
        $localDateTime = Literal::localDateTime();

        $this->assertEquals(new LocalDateTime(), $localDateTime);
    }

    public function testLocalDateTimeWithTimezone(): void
    {
        $localDateTime = Literal::localDateTime("America/Los Angeles");

        $this->assertEquals(new LocalDateTime(new Map(["timezone" => new String_("America/Los Angeles")])), $localDateTime);
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
        $localDatetime = Literal::localDateTimeYMD($year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYMDMissingMonth(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDateTimeYMD(2000, null, 17);

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
        $localDatetime = Literal::localDateTimeYWD($year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYWDMissingWeek(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDateTimeYWD(2000, null, 17);

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
        $localDatetime = Literal::localDateTimeYQD($year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYQDMissingQuarter(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDateTimeYQD(2000, null, 17);

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
        $localDatetime = Literal::localDateTimeYD($year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond);

        $this->assertEquals($expected, $localDatetime);
    }

    public function testLocalDateTimeYDMissingOrdinalDay(): void
    {
        $this->expectException(LogicException::class);

        $datetime = Literal::localDateTimeYD(2000, null, 17);

        $datetime->toQuery();
    }

    public function testLocalDatetimeString(): void
    {
        $localDatetime = Literal::localDateTimeString("2015-W30-2T214032.142");

        $this->assertEquals(new LocalDateTime(new String_("2015-W30-2T214032.142")), $localDatetime);
    }

    public function testLocalTimeCurrentWithoutTimezone(): void
    {
        $localTime = Literal::localTimeCurrent();
        $this->assertEquals(new LocalTime(), $localTime);
    }

    public function testLocalTimeCurrentWithTimezone(): void
    {
        $localTime = Literal::localTimeCurrent("America/Los Angeles");
        $this->assertEquals(new LocalTime(new Map(["timezone" => new String_("America/Los Angeles")])), $localTime);
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
        $this->assertEquals(new LocalTime(new String_("21:40:32.142")), $localTime);
    }

    public function testTimeCurrentWithoutTimezone(): void
    {
        $time = Literal::time();
        $this->assertEquals($time, new Time());
    }

    public function testTimeCurrentWithTimezone(): void
    {
        $time = Literal::time("America/Los Angeles");
        $this->assertEquals($time, new Time(new Map(["timezone" => new String_("America/Los Angeles")])));
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
        $this->assertEquals($time, new Time(new String_("21:40:32.142+0100")));
    }

    public function provideDateYMDData(): array
    {
        return [
            [2000, null, null, new Date(new Map(["year" => new Integer(2000)]))],
            [2000, 12, null, new Date(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [2000, 12, 17, new Date(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(17)]))],
            [new Integer(2000), null, null, new Date(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(12), null, new Date(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [new Integer(2000), new Integer(12), new Integer(17), new Date(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(17)]))],

        ];
    }

    public function provideDateYWDData(): array
    {
        return [
            [2000, null, null, new Date(new Map(["year" => new Integer(2000)]))],
            [2000, 12, null, new Date(new Map(["year" => new Integer(2000), "week" => new Integer(12)]))],
            [2000, 12, 17, new Date(new Map(["year" => new Integer(2000), "week" => new Integer(12), "dayOfWeek" => new Integer(17)]))],
            [new Integer(2000), null, null, new Date(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(12), null, new Date(new Map(["year" => new Integer(2000), "week" => new Integer(12)]))],
            [new Integer(2000), new Integer(12), new Integer(17), new Date(new Map(["year" => new Integer(2000), "week" => new Integer(12), "dayOfWeek" => new Integer(17)]))],

        ];
    }

    public function provideDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 12, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [2000, 12, 15, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15)]))],
            [2000, 12, 15, 8, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8)]))],
            [2000, 12, 15, 8, 25, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(12), null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [new Integer(2000), new Integer(12), new Integer(15), null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 9, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9)]))],
            [2000, 9, 4, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4)]))],
            [2000, 9, 4, 8, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8)]))],
            [2000, 9, 4, 8, 25, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(9), null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9)]))],
            [new Integer(2000), new Integer(9), new Integer(4), null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3)]))],
            [2000, 3, 4, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4)]))],
            [2000, 3, 4, 8, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8)]))],
            [2000, 3, 4, 8, 25, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3)]))],
            [new Integer(2000), new Integer(3), new Integer(4), null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],
        ];
    }

    public function provideDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3)]))],
            [2000, 3, 8, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8)]))],
            [2000, 3, 8, 25, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 3, 8, 25, 44, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 3, 8, 25, 44, 18, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 3, 8, 25, 44, 18, 6, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3)]))],
            [new Integer(2000), new Integer(3), new Integer(8), null, null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31), "timezone" => new String_("America/Los Angeles")]))],
        ];
    }

    public function provideLocalDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 12, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [2000, 12, 15, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15)]))],
            [2000, 12, 15, 8, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8)]))],
            [2000, 12, 15, 8, 25, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null,new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(12), null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12)]))],
            [new Integer(2000), new Integer(12), new Integer(15), null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(new Map(["year" => new Integer(2000), "month" => new Integer(12), "day" => new Integer(15), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
        ];
    }

    public function provideLocalDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 9, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9)]))],
            [2000, 9, 4, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4)]))],
            [2000, 9, 4, 8, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8)]))],
            [2000, 9, 4, 8, 25, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(9), null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9)]))],
            [new Integer(2000), new Integer(9), new Integer(4), null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(new Map(["year" => new Integer(2000), "week" => new Integer(9), "dayOfWeek" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
        ];
    }

    public function provideLocalDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 3, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3)]))],
            [2000, 3, 4, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4)]))],
            [2000, 3, 4, 8, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8)]))],
            [2000, 3, 4, 8, 25, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3)]))],
            [new Integer(2000), new Integer(3), new Integer(4), null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(new Map(["year" => new Integer(2000), "quarter" => new Integer(3), "dayOfQuarter" => new Integer(4), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
        ];
    }

    public function provideLocalDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [2000, 3, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3)]))],
            [2000, 3, 8, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8)]))],
            [2000, 3, 8, 25, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [2000, 3, 8, 25, 44, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [2000, 3, 8, 25, 44, 18, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [2000, 3, 8, 25, 44, 18, 6, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000)]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3)]))],
            [new Integer(2000), new Integer(3), new Integer(8), null, null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6)]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(new Map(["year" => new Integer(2000), "ordinalDay" => new Integer(3), "hour" => new Integer(8), "minute" => new Integer(25), "second" => new Integer(44), "millisecond" => new Integer(18), "microsecond" => new Integer(6), "nanosecond" => new Integer(31)]))],
        ];
    }

    public function provideLocalTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new LocalTime(new Map(["hour" => new Integer(11)]))],
            [11, 23, null, null, null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23)]))],
            [11, 23, 2, null, null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2)]))],
            [11, 23, 2, 54, null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54)]))],
            [11, 23, 2, 54, 8, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8)]))],
            [11, 23, 2, 54, 8, 29, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8), "nanosecond" => new Integer(29)]))],

            // types
            [new Integer(11), null, null, null, null, null, new LocalTime(new Map(["hour" => new Integer(11)]))],
            [new Integer(11), new Integer(23), null, null, null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23)]))],
            [new Integer(11), new Integer(23), new Integer(2), null, null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), null, null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), null, new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), new Integer(29), new LocalTime(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8), "nanosecond" => new Integer(29)]))],
        ];
    }

    public function provideTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new Time(new Map(["hour" => new Integer(11)]))],
            [11, 23, null, null, null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23)]))],
            [11, 23, 2, null, null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2)]))],
            [11, 23, 2, 54, null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54)]))],
            [11, 23, 2, 54, 8, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8)]))],
            [11, 23, 2, 54, 8, 29, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8), "nanosecond" => new Integer(29)]))],

            // types
            [new Integer(11), null, null, null, null, null, new Time(new Map(["hour" => new Integer(11)]))],
            [new Integer(11), new Integer(23), null, null, null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23)]))],
            [new Integer(11), new Integer(23), new Integer(2), null, null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), null, null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), null, new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8)]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), new Integer(29), new Time(new Map(["hour" => new Integer(11), "minute" => new Integer(23), "second" => new Integer(2), "millisecond" => new Integer(54), "microsecond" => new Integer(8), "nanosecond" => new Integer(29)]))],
        ];
    }
}
