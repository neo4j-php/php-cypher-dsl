<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Date;
use WikibaseSolutions\CypherDSL\Expressions\Functions\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Func;
use WikibaseSolutions\CypherDSL\Expressions\Functions\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Functions\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Point;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Time;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Helper class to construct literals.
 *
 * @note This class should only contain static methods
 */
final class Literal
{
    /**
     * Prevent the construction of this class by making the constructor private.
     */
    private function __construct()
    {
    }

    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * @param mixed $literal The literal to construct
     * @return String_|Boolean|Decimal|List_|Map
     */
    public static function literal($literal)
    {
        if (is_string($literal)) {
            return self::string($literal);
        }

        if (is_bool($literal)) {
            return self::boolean($literal);
        }

        if (is_int($literal) || is_float($literal)) {
            return self::decimal($literal);
        }

        if (is_array($literal)) {
            return array_is_list($literal) ? self::list($literal) : self::map($literal);
        }

        throw new InvalidArgumentException(
            "The literal type " . is_object($literal) ? get_class($literal) : gettype($literal) . " is not supported by Cypher"
        );
    }

    /**
     * Creates a new boolean.
     *
     * @param bool $value
     * @return Boolean
     */
    public static function boolean(bool $value): Boolean
    {
        // PhpStorm warns about a type error here, this is a bug.
        // @see https://youtrack.jetbrains.com/issue/WI-68030
        return new Boolean($value);
    }

    /**
     * Creates a new string.
     *
     * @param string $value
     * @return String_
     */
    public static function string(string $value): String_
    {
        return new String_($value);
    }

    /**
     * Creates a new decimal literal.
     *
     * @param int|float $value
     * @return Decimal
     */
    public static function decimal($value): Decimal
    {
        return new Decimal($value);
    }

    /**
     * Creates a new list literal.
     *
     * @param array $value
     * @return List_
     */
    public static function list(array $value): List_
    {
        return new List_(array_map([self::class, 'literal'], $value));
    }

    /**
     * Creates a new map literal.
     *
     * @param array $value
     * @return Map
     */
    public static function map(array $value): Map
    {
        return new Map(array_map([self::class, 'literal'], $value));
    }

    /**
     * Retrieves the current Date value, optionally for a different time zone. In reality, this function just returns
     * a call to the "date()" function.
     *
     * @param null|string|StringType $timezone
     * @return Date
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-current
     */
    public static function date($timezone = null): Date
    {
        if ($timezone === null) {
            return Func::date();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }

        return Func::date(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates a date from the given year, month and day.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     * @return Date
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-calendar
     */
    public static function dateYMD($year, $month = null, $day = null): Date
    {
        return Func::date(self::makeTemporalMap([
            "year" => $year,
            "month" => $month,
            "day" => $day,
        ]));
    }

    /**
     * Creates a date from the given year, week and weekday.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $weekday
     * @return Date
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-week
     */
    public static function dateYWD($year, $week = null, $weekday = null): Date
    {
        return Func::date(self::makeTemporalMap([
            "year" => $year,
            "week" => $week,
            "dayOfWeek" => $weekday,
        ]));
    }

    /**
     * Creates a date from the given string.
     *
     * @param string|StringType $date
     * @return Date
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-create-string
     */
    public static function dateString($date): Date
    {
        if (!$date instanceof StringType) {
            $date = self::string($date);
        }

        return Func::date($date);
    }

    /**
     * Retrieves the current DateTime value, optionally for a different time zone. In reality, this
     * function just returns a call to the "datetime()" function.
     *
     * @param string|StringType $timezone
     * @return DateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-current
     */
    public static function dateTime($timezone = null): DateTime
    {
        if ($timezone === null) {
            return Func::datetime();
        }

        if (!$timezone instanceof StringType) {
            $timezone = self::string($timezone);
        }

        return Func::datetime(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates a date from the given year, month, day and time values.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @param null|string|StringType $timezone
     * @return DateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-calendar
     */
    public static function dateTimeYMD(
        $year,
        $month = null,
        $day = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null,
        $timezone = null
    ): DateTime {
        return Func::datetime(self::makeTemporalMap([
            "year" => $year,
            "month" => $month,
            "day" => $day,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
            "timezone" => $timezone,
        ]));
    }

    /**
     * Creates a datetime with the specified year, week, dayOfWeek, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $dayOfWeek
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @param null|string|StringType $timezone
     * @return DateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-week
     */
    public static function dateTimeYWD(
        $year,
        $week = null,
        $dayOfWeek = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null,
        $timezone = null
    ): DateTime {
        return Func::datetime(self::makeTemporalMap([
            "year" => $year,
            "week" => $week,
            "dayOfWeek" => $dayOfWeek,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
            "timezone" => $timezone,
        ]));
    }

    /**
     * Creates a datetime with the specified year, quarter, dayOfQuarter, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $quarter
     * @param null|int|NumeralType $dayOfQuarter
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @param null|string|StringType $timezone
     * @return DateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-quarter
     */
    public static function dateTimeYQD(
        $year,
        $quarter = null,
        $dayOfQuarter = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null,
        $timezone = null
    ): DateTime {
        return Func::datetime(self::makeTemporalMap([
            "year" => $year,
            "quarter" => $quarter,
            "dayOfQuarter" => $dayOfQuarter,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
            "timezone"   => $timezone,
        ]));
    }

    /**
     * Creates a datetime with the specified year, ordinalDay, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $ordinalDay
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @param null|string|StringType $timezone
     * @return DateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-ordinal
     */
    public static function dateTimeYD(
        $year,
        $ordinalDay = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null,
        $timezone = null
    ): DateTime {
        return Func::datetime(self::makeTemporalMap([
            "year" => $year,
            "ordinalDay" => $ordinalDay,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
            "timezone"   => $timezone,
        ]));
    }

    /**
     * Creates a datetime by parsing a string representation of a temporal value
     *
     * @param string|StringType $dateString
     * @return DateTime
     */
    public static function dateTimeString($dateString): DateTime
    {
        if (!($dateString instanceof StringType)) {
            $dateString = self::string($dateString);
        }

        return Func::datetime($dateString);
    }

    /**
     * Creates the current localDateTime value
     *
     * @param null|string|StringType $timezone
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-current
     */
    public static function localDateTime($timezone = null): LocalDateTime
    {
        if ($timezone === null) {
            return Func::localdatetime();
        }

        if (!$timezone instanceof StringType) {
            $timezone = self::string($timezone);
        }

        return Func::localdatetime(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates a LocalDateTime value with specified year, month, day and time props
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-calendar
     */
    public static function localDateTimeYMD(
        $year,
        $month = null,
        $day = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null
    ): LocalDateTime {
        return Func::localdatetime(self::makeTemporalMap([
            "year" => $year,
            "month" => $month,
            "day" => $day,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
        ]));
    }

    /**
     * Creates a LocalDateTime value with the specified year, week, dayOfWeek, hour, minute,
     * second, millisecond, microsecond and nanosecond component value
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $dayOfWeek
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-week
     */
    public static function localDateTimeYWD(
        $year,
        $week = null,
        $dayOfWeek = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null
    ): LocalDateTime {
        return Func::localdatetime(self::makeTemporalMap([
            "year" => $year,
            "week" => $week,
            "dayOfWeek" => $dayOfWeek,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
        ]));
    }

    /**
     * Creates a LocalDateTime value with the specified year, quarter, dayOfQuarter, hour, minute, second, millisecond, microsecond and nanosecond component values
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $quarter
     * @param null|int|NumeralType $dayOfQuarter
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-quarter
     */
    public static function localDateTimeYQD(
        $year,
        $quarter = null,
        $dayOfQuarter = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null
    ): LocalDateTime {
        return Func::localdatetime(self::MakeTemporalMap([
            "year" => $year,
            "quarter" => $quarter,
            "dayOfQuarter" => $dayOfQuarter,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
        ]));
    }

    /**
     * Creates a LocalDateTime value with the specified year, ordinalDay, hour, minute, second, millisecond, microsecond and nanosecond component values
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $ordinalDay
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-ordinal
     */
    public static function localDateTimeYD(
        $year,
        $ordinalDay = null,
        $hour = null,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null
    ): LocalDateTime {
        return Func::localdatetime(self::makeTemporalMap([
            "year" => $year,
            "ordinalDay" => $ordinalDay,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
        ]));
    }

    /**
     * Creates the LocalDateTime value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $localDateTimeString
     * @return LocalDateTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-create-string
     */
    public static function localDateTimeString($localDateTimeString): LocalDateTime
    {
        if (!$localDateTimeString instanceof StringType) {
            $localDateTimeString = self::string($localDateTimeString);
        }

        return Func::localdatetime($localDateTimeString);
    }

    /**
     * Creates the current LocalTime value
     *
     * @param null|string|StringType $timezone
     * @return LocalTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-current
     */
    public static function localTimeCurrent($timezone = null): LocalTime
    {
        if ($timezone === null) {
            return Func::localtime();
        }

        if (!$timezone instanceof StringType) {
            $timezone = self::string($timezone);
        }

        return Func::localtime(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates a LocalTime value with the specified hour, minute, second, millisecond, microsecond and nanosecond component values
     *
     * @param int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @return LocalTime
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-create
     */
    public static function localTime(
        $hour,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null
    ): LocalTime {
        return Func::localtime(self::makeTemporalMap([
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
        ]));
    }

    /**
     * Creates the LocalTime value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $localTimeString
     * @return LocalTime
     */
    public static function localTimeString($localTimeString): LocalTime
    {
        if (!($localTimeString instanceof StringType)) {
            $localTimeString = self::string($localTimeString);
        }

        return Func::localtime($localTimeString);
    }

    /**
     * Creates the current Time value
     *
     * @param null|string|StringType $timezone
     * @return Time
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-current
     */
    public static function time($timezone = null): Time
    {
        if ($timezone === null) {
            return Func::time();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }

        return Func::time(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates  a Time value with the specified hour, minute, second, millisecond, microsecond, nanosecond and timezone component values
     *
     * @param int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
     * @param null|string|StringType $timezone
     * @return Time
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create
     */
    public static function timeHMS(
        $hour,
        $minute = null,
        $second = null,
        $millisecond = null,
        $microsecond = null,
        $nanosecond = null,
        $timezone = null
    ): Time {
        return Func::time(self::makeTemporalMap([
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond,
            "timezone" => $timezone,
        ]));
    }

    /**
     * Creates the Time value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $timeString
     * @return Time
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create-string
     */
    public static function timeString($timeString): Time
    {
        if (!($timeString instanceof StringType)) {
            $timeString = self::string($timeString);
        }

        return Func::time($timeString);
    }

    /**
     * Creates a 2d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @return Point
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-2d
     */
    public static function point2d($x, $y): Point
    {
        if (!($x instanceof NumeralType)) {
            $x = self::decimal($x);
        }

        if (!($y instanceof NumeralType)) {
            $y = self::decimal($y);
        }

        $map = [
            "x" => $x,
            "y" => $y,
        ];

        $map["crs"] = self::string("cartesian");

        return Func::point(Query::map($map));
    }

    /**
     * Creates a 3d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @param float|int|NumeralType $z
     * @return Point
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-3d
     */
    public static function point3d($x, $y, $z): Point
    {
        if (!($x instanceof NumeralType)) {
            $x = self::decimal($x);
        }

        if (!($y instanceof NumeralType)) {
            $y = self::decimal($y);
        }

        if (!($z instanceof NumeralType)) {
            $z = self::decimal($z);
        }

        $map = [
            "x" => $x,
            "y" => $y,
            "z" => $z,
        ];

        $map["crs"] = self::string("cartesian-3D");

        return Func::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @return Point
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point2dWGS84($longitude, $latitude): Point
    {
        if (!($longitude instanceof NumeralType)) {
            $longitude = self::decimal($longitude);
        }

        if (!($latitude instanceof NumeralType)) {
            $latitude = self::decimal($latitude);
        }

        $map = [
            "longitude" => $longitude,
            "latitude" => $latitude,
        ];

        $map["crs"] = self::string("WGS-84");

        return Func::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @param float|int|NumeralType $height
     * @return Point
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point3dWGS84($longitude, $latitude, $height): Point
    {
        if (!($longitude instanceof NumeralType)) {
            $longitude = self::decimal($longitude);
        }

        if (!($latitude instanceof NumeralType)) {
            $latitude = self::decimal($latitude);
        }

        if (!($height instanceof NumeralType)) {
            $height = self::decimal($height);
        }

        $map = [
            "longitude" => $longitude,
            "latitude" => $latitude,
            "height" => $height,
        ];

        $map["crs"] = self::string("WGS-84-3D");

        return Func::point(Query::map($map));
    }

    /**
     * Prepares the variables to be used by temporal (i.e. time-like) CYPHER-functions.
     *
     * The following are done:
     * - For all $variables except for the 'timezone' it is checked if any one of them exists without the previous variable existing, up to & including the 'second' variable.
     * - If a 'second' variable is encountered, it is checked if 'seconds' is not-null when milliseconds/microseconds/nanoseconds are provided.
     * - All variables except 'timezone' are made into NumeralType.
     * - 'timezone' is made into StringLiteral.
     *
     * @param array $variables
     * @return Map
     */
    private static function makeTemporalMap(array $variables): Map
    {
        $map = [];

        $nullEncountered = false;
        $secondsFound = false;

        foreach ($variables as $key => $variable) {
            if ($variable === null) {
                $nullEncountered = true;

                continue;
            }

            if ($key === 'timezone') {
                // Timezone can always be added, and is a string.
                $map[$key] = self::convertToString($variable);
            } else {
                if (!$secondsFound && $nullEncountered) {
                    // Check if none of the previous, i.e. more important components, are null.
                    // sub-second values are not interdependent, but seconds must then be provided.
                    throw new \LogicException("The key $key can only be provided when all more significant components are provided as well.");
                }

                if ($key === 'second') {
                    $secondsFound = true;
                }

                $map[$key] = self::convertToNumeral($variable);
            }
        }

        return Query::map($map);
    }

    /**
     * Convert the given value to a numeral.
     *
     * @param NumeralType|float|int $var
     * @return Decimal
     */
    private static function convertToNumeral($var): NumeralType
    {
        if ($var instanceof NumeralType) {
            return $var;
        }

        return self::decimal($var);
    }

    /**
     * Convert the given value to a string.
     *
     * @param $var
     * @return StringType
     */
    private static function convertToString($var): StringType
    {
        if ($var instanceof StringType) {
            return $var;
        }

        return self::string($var);
    }
}
