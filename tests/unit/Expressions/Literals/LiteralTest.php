<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use Iterator;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Literal
 */
final class LiteralTest extends TestCase
{
    public static function provideLiteralDeductionFailure(): array
    {
        return [
            [new class()
            {
            }, ],
            [null],
        ];
    }

    public static function provideDateYMDData(): array
    {
        return [
            [2000, null, null, new Date(Literal::map(["year" => 2000]))],
            [2000, 12, null, new Date(Literal::map(["year" => 2000, "month" => 12]))],
            [2000, 12, 17, new Date(Literal::map(["year" => 2000, "month" => 12, "day" => 17]))],
            [new Integer(2000), null, null, new Date(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(12), null, new Date(Literal::map(["year" => 2000, "month" => 12]))],
            [new Integer(2000), new Integer(12), new Integer(17), new Date(Literal::map(["year" => 2000, "month" => 12, "day" => 17]))],

        ];
    }

    public static function provideDateYWDData(): array
    {
        return [
            [2000, null, null, new Date(Literal::map(["year" => 2000]))],
            [2000, 12, null, new Date(Literal::map(["year" => 2000, "week" => 12]))],
            [2000, 12, 17, new Date(Literal::map(["year" => 2000, "week" => 12, "dayOfWeek" => 17]))],
            [new Integer(2000), null, null, new Date(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(12), null, new Date(Literal::map(["year" => 2000, "week" => 12]))],
            [new Integer(2000), new Integer(12), new Integer(17), new Date(Literal::map(["year" => 2000, "week" => 12, "dayOfWeek" => 17]))],

        ];
    }

    public static function provideDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [2000, 12, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12]))],
            [2000, 12, 15, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15]))],
            [2000, 12, 15, 8, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8]))],
            [2000, 12, 15, 8, 25, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(12), null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12]))],
            [new Integer(2000), new Integer(12), new Integer(15), null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],
        ];
    }

    public static function provideDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [2000, 9, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9]))],
            [2000, 9, 4, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4]))],
            [2000, 9, 4, 8, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8]))],
            [2000, 9, 4, 8, 25, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(9), null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9]))],
            [new Integer(2000), new Integer(9), new Integer(4), null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],
        ];
    }

    public static function provideDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [2000, 3, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3]))],
            [2000, 3, 4, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4]))],
            [2000, 3, 4, 8, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8]))],
            [2000, 3, 4, 8, 25, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3]))],
            [new Integer(2000), new Integer(3), new Integer(4), null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],
        ];
    }

    public static function provideDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $timezone, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [2000, 3, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3]))],
            [2000, 3, 8, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8]))],
            [2000, 3, 8, 25, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25]))],
            [2000, 3, 8, 25, 44, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 3, 8, 25, 44, 18, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 3, 8, 25, 44, 18, 6, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, "America/Los Angeles", new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3]))],
            [new Integer(2000), new Integer(3), new Integer(8), null, null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), null, null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), null, null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), null, new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new String_("America/Los Angeles"), new DateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31, "timezone" => "America/Los Angeles"]))],
        ];
    }

    public static function provideLocalDatetimeYMDData(): array
    {
        // [$year, $month, $day, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [2000, 12, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12]))],
            [2000, 12, 15, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15]))],
            [2000, 12, 15, 8, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8]))],
            [2000, 12, 15, 8, 25, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25]))],
            [2000, 12, 15, 8, 25, 44, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 12, 15, 8, 25, 44, 18, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 12, 15, 8, 25, 44, 18, 6, 31, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(12), null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12]))],
            [new Integer(2000), new Integer(12), new Integer(15), null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(12), new Integer(15), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(Literal::map(["year" => 2000, "month" => 12, "day" => 15, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
        ];
    }

    public static function provideLocalDatetimeYWDData(): array
    {
        // [$year, $week, $dayOfWeek, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [2000, 9, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9]))],
            [2000, 9, 4, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4]))],
            [2000, 9, 4, 8, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8]))],
            [2000, 9, 4, 8, 25, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25]))],
            [2000, 9, 4, 8, 25, 44, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 9, 4, 8, 25, 44, 18, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 9, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(9), null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9]))],
            [new Integer(2000), new Integer(9), new Integer(4), null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(9), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(Literal::map(["year" => 2000, "week" => 9, "dayOfWeek" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
        ];
    }

    public static function provideLocalDatetimeYQDData(): array
    {
        // [$year, $quarter, $dayOfQuarter, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [2000, 3, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3]))],
            [2000, 3, 4, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4]))],
            [2000, 3, 4, 8, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8]))],
            [2000, 3, 4, 8, 25, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25]))],
            [2000, 3, 4, 8, 25, 44, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 3, 4, 8, 25, 44, 18, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 3, 4, 8, 25, 44, 18, 6, 31, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3]))],
            [new Integer(2000), new Integer(3), new Integer(4), null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(3), new Integer(4), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(Literal::map(["year" => 2000, "quarter" => 3, "dayOfQuarter" => 4, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
        ];
    }

    public static function provideLocalDatetimeYQData(): array
    {
        // [$year, $ordinalDay, $hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [2000, null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [2000, 3, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3]))],
            [2000, 3, 8, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8]))],
            [2000, 3, 8, 25, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25]))],
            [2000, 3, 8, 25, 44, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44]))],
            [2000, 3, 8, 25, 44, 18, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [2000, 3, 8, 25, 44, 18, 6, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [2000, 3, 8, 25, 44, 18, 6, 31, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],

            // types
            [new Integer(2000), null, null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000]))],
            [new Integer(2000), new Integer(3), null, null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3]))],
            [new Integer(2000), new Integer(3), new Integer(8), null, null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), null, null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), null, null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), null, null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), null, new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6]))],
            [new Integer(2000), new Integer(3), new Integer(8), new Integer(25), new Integer(44), new Integer(18), new Integer(6), new Integer(31), new LocalDateTime(Literal::map(["year" => 2000, "ordinalDay" => 3, "hour" => 8, "minute" => 25, "second" => 44, "millisecond" => 18, "microsecond" => 6, "nanosecond" => 31]))],
        ];
    }

    public static function provideLocalTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new LocalTime(Literal::map(["hour" => 11]))],
            [11, 23, null, null, null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23]))],
            [11, 23, 2, null, null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2]))],
            [11, 23, 2, 54, null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54]))],
            [11, 23, 2, 54, 8, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8]))],
            [11, 23, 2, 54, 8, 29, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8, "nanosecond" => 29]))],

            // types
            [new Integer(11), null, null, null, null, null, new LocalTime(Literal::map(["hour" => 11]))],
            [new Integer(11), new Integer(23), null, null, null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23]))],
            [new Integer(11), new Integer(23), new Integer(2), null, null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), null, null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), null, new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), new Integer(29), new LocalTime(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8, "nanosecond" => 29]))],
        ];
    }

    public static function provideTimeData(): array
    {
        // [$hour, $minute, $second, $millisecond, $microsecond, $nanosecond, $expected]
        return [
            [11, null, null, null, null, null, new Time(Literal::map(["hour" => 11]))],
            [11, 23, null, null, null, null, new Time(Literal::map(["hour" => 11, "minute" => 23]))],
            [11, 23, 2, null, null, null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2]))],
            [11, 23, 2, 54, null, null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54]))],
            [11, 23, 2, 54, 8, null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8]))],
            [11, 23, 2, 54, 8, 29, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8, "nanosecond" => 29]))],

            // types
            [new Integer(11), null, null, null, null, null, new Time(Literal::map(["hour" => 11]))],
            [new Integer(11), new Integer(23), null, null, null, null, new Time(Literal::map(["hour" => 11, "minute" => 23]))],
            [new Integer(11), new Integer(23), new Integer(2), null, null, null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), null, null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), null, new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8]))],
            [new Integer(11), new Integer(23), new Integer(2), new Integer(54), new Integer(8), new Integer(29), new Time(Literal::map(["hour" => 11, "minute" => 23, "second" => 2, "millisecond" => 54, "microsecond" => 8, "nanosecond" => 29]))],
        ];
    }

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
        $this->expectException(TypeError::class);

        Literal::literal(new stdClass());
    }

    public function testStringable(): void
    {
        $stringable = Literal::literal(new class()
        {
            public function __toString()
            {
                return 'Testing is a virtue!';
            }
        });

        $this->assertInstanceOf(String_::class, $stringable);
    }

    /**
     * @dataProvider provideLiteralDeductionFailure
     */
    public function testLiteralDeductionFailure($value): void
    {
        $this->expectException(TypeError::class);
        Literal::literal($value);
    }

    public function testNumberInteger(): void
    {
        $integer = Literal::number(1);

        $this->assertInstanceOf(Integer::class, $integer);
    }

    public function testNumberFloat(): void
    {
        $float = Literal::number(1.0);

        $this->assertInstanceOf(Float_::class, $float);
    }

    public function testNumberNoString(): void
    {
        $this->expectException(TypeError::class);

        Literal::number('55');
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
        $list = Literal::list(new class implements Iterator
        {
            public function current(): int
            {
                return 1;
            }

            public function next(): void
            {
            }

            public function key(): string
            {
                return '';
            }

            public function valid(): bool
            {
                static $i = 0;

                return $i++ < 10;
            }

            public function rewind(): void
            {
            }
        });

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

        $this->assertEquals(new Point(Literal::map(["x" => 1, "y" => 2, "crs" => "cartesian"])), $point);

        $point = Literal::point2d(
            new Integer(1),
            new Integer(2)
        );

        $this->assertEquals(new Point(Literal::map(["x" => 1, "y" => 2, "crs" => "cartesian"])), $point);
    }

    public function testPoint3d(): void
    {
        $point = Literal::point3d(1, 2, 3);

        $this->assertEquals(new Point(Literal::map(["x" => 1, "y" => 2, "z" => 3, "crs" => "cartesian-3D"])), $point);

        $point = Literal::point3d(
            new Integer(1),
            new Integer(2),
            new Integer(3)
        );

        $this->assertEquals(new Point(Literal::map(["x" => 1, "y" => 2, "z" => 3, "crs" => "cartesian-3D"])), $point);
    }

    public function testPoint2dWGS84(): void
    {
        $point = Literal::point2dWGS84(1, 2);

        $this->assertEquals(new Point(Literal::map(["longitude" => 1, "latitude" => 2, "crs" => "WGS-84"])), $point);

        $point = Literal::point2dWGS84(
            new Integer(1),
            new Integer(2)
        );

        $this->assertEquals(new Point(Literal::map(["longitude" => 1, "latitude" => 2, "crs" => "WGS-84"])), $point);
    }

    public function testPoint3dWGS84(): void
    {
        $point = Literal::point3dWGS84(1, 2, 3);

        $this->assertEquals(new Point(Literal::map(["longitude" => 1, "latitude" => 2, "height" => 3, "crs" => "WGS-84-3D"])), $point);

        $point = Literal::point3dWGS84(
            new Integer(1),
            new Integer(2),
            new Integer(3)
        );

        $this->assertEquals(new Point(Literal::map(["longitude" => 1, "latitude" => 2, "height" => 3, "crs" => "WGS-84-3D"])), $point);
    }

    public function testDate(): void
    {
        $date = Literal::date();

        $this->assertEquals(new Date(), $date);
    }

    public function testDateTimezone(): void
    {
        $date = Literal::date("Europe/Amsterdam");

        $this->assertEquals(new Date(Literal::map(["timezone" => "Europe/Amsterdam"])), $date);

        $date = Literal::date(new String_("Europe/Amsterdam"));

        $this->assertEquals(new Date(Literal::map(["timezone" => "Europe/Amsterdam"])), $date);
    }

    /**
     * @dataProvider provideDateYMDData
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

        $this->assertEquals(new DateTime(Literal::map(["timezone" => "America/Los Angeles"])), $datetime);
    }

    /**
     * @dataProvider provideDatetimeYMDData
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

        $this->assertEquals(new LocalDateTime(Literal::map(["timezone" => "America/Los Angeles"])), $localDateTime);
    }

    /**
     * @dataProvider provideLocalDatetimeYMDData
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
        $this->assertEquals(new LocalTime(Literal::map(["timezone" => "America/Los Angeles"])), $localTime);
    }

    /**
     * @dataProvider provideLocalTimeData
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
        $this->assertEquals($time, new Time(Literal::map(["timezone" => "America/Los Angeles"])));
    }

    /**
     * @dataProvider provideTimeData
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
}
