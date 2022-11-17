<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use LogicException;
use Traversable;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Helper class to construct literals.
 *
 * @note This class should only contain static methods
 */
final class Literal
{
    use CastTrait;
    use ErrorTrait;

    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * @param mixed $literal The literal to construct
     *
     * @throws TypeError when the type could not be deduced
     *
     * @return Boolean|Float_|Integer|List_|Map|String_
     */
    public static function literal($literal)
    {
        if (is_string($literal)) {
            return self::string($literal);
        }

        if (is_bool($literal)) {
            return self::boolean($literal);
        }

        if (is_int($literal)) {
            return self::integer($literal);
        }

        if (is_float($literal)) {
            return self::float($literal);
        }

        if (is_array($literal)) {
            return array_is_list($literal) ? self::list($literal) : self::map($literal);
        }

        if (is_object($literal) && method_exists($literal, '__toString')) {
            return self::string($literal->__toString());
        }

        throw new TypeError("The literal type " . get_debug_type($literal) . " is not supported by Cypher");
    }

    /**
     * Creates a new numeral literal.
     *
     * @param float|int $value
     *
     * @return Float_|Integer
     *
     * @see        Literal::number()
     * @deprecated
     */
    public static function decimal($value)
    {
        return self::number($value);
    }

    /**
     * Creates a new numeral literal.
     *
     * @param float|int $value
     *
     * @return Float_|Integer
     */
    public static function number($value)
    {
        self::assertClass('value', ['int', 'float'], $value);

        return is_int($value) ? self::integer($value) : self::float($value);
    }

    /**
     * Creates a new boolean.
     */
    public static function boolean(bool $value): Boolean
    {
        // PhpStorm warns about a type error here, this is a bug.
        // @see https://youtrack.jetbrains.com/issue/WI-39239
        return new Boolean($value);
    }

    /**
     * Creates a new string.
     */
    public static function string(string $value): String_
    {
        return new String_($value);
    }

    /**
     * Creates a new integer.
     */
    public static function integer(int $value): Integer
    {
        // PhpStorm warns about a type error here, this is a bug.
        // @see https://youtrack.jetbrains.com/issue/WI-39239
        return new Integer($value);
    }

    /**
     * Creates a new float.
     */
    public static function float(float $value): Float_
    {
        return new Float_($value);
    }

    /**
     * Creates a new list literal.
     *
     * @param array|iterable $value
     */
    public static function list(iterable $value): List_
    {
        if ($value instanceof Traversable) {
            $value = iterator_to_array($value);
        }

        return new List_(array_map([self::class, 'toAnyType'], $value));
    }

    /**
     * Creates a new map literal.
     */
    public static function map(array $value): Map
    {
        return new Map(array_map([self::class, 'toAnyType'], $value));
    }

    /**
     * Retrieves the current Date value, optionally for a different time zone. In reality, this function just returns
     * a call to the "date()" function.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-current
     */
    public static function date($timezone = null): Date
    {
        if ($timezone === null) {
            return Procedure::date();
        }

        return Procedure::date(Query::map(["timezone" => self::toStringType($timezone)]));
    }

    /**
     * Creates a date from the given year, month and day.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-calendar
     */
    public static function dateYMD($year, $month = null, $day = null): Date
    {
        return Procedure::date(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "month" => $month,
                    "day" => $day,
                ]
            )
        );
    }

    /**
     * Creates a date from the given year, week and weekday.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $weekday
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-week
     */
    public static function dateYWD($year, $week = null, $weekday = null): Date
    {
        return Procedure::date(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "week" => $week,
                    "dayOfWeek" => $weekday,
                ]
            )
        );
    }

    /**
     * Creates a date from the given string.
     *
     * @param string|StringType $date
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-create-string
     */
    public static function dateString($date): Date
    {
        return Procedure::date(self::toStringType($date));
    }

    /**
     * Retrieves the current DateTime value, optionally for a different time zone. In reality, this
     * function just returns a call to the "datetime()" function.
     *
     * @param string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-current
     */
    public static function dateTime($timezone = null): DateTime
    {
        if ($timezone === null) {
            return Procedure::datetime();
        }

        return Procedure::datetime(Query::map(["timezone" => self::toStringType($timezone)]));
    }

    /**
     * Creates a date from the given year, month, day and time values.
     *
     * @param int|NumeralType        $year
     * @param null|int|NumeralType   $month
     * @param null|int|NumeralType   $day
     * @param null|int|NumeralType   $hour
     * @param null|int|NumeralType   $minute
     * @param null|int|NumeralType   $second
     * @param null|int|NumeralType   $millisecond
     * @param null|int|NumeralType   $microsecond
     * @param null|int|NumeralType   $nanosecond
     * @param null|string|StringType $timezone
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
        return Procedure::datetime(
            self::makeTemporalMap(
                [
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
                ]
            )
        );
    }

    /**
     * Creates a datetime with the specified year, week, dayOfWeek, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType        $year
     * @param null|int|NumeralType   $week
     * @param null|int|NumeralType   $dayOfWeek
     * @param null|int|NumeralType   $hour
     * @param null|int|NumeralType   $minute
     * @param null|int|NumeralType   $second
     * @param null|int|NumeralType   $millisecond
     * @param null|int|NumeralType   $microsecond
     * @param null|int|NumeralType   $nanosecond
     * @param null|string|StringType $timezone
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
        return Procedure::datetime(
            self::makeTemporalMap(
                [
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
                ]
            )
        );
    }

    /**
     * Creates a datetime with the specified year, quarter, dayOfQuarter, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType        $year
     * @param null|int|NumeralType   $quarter
     * @param null|int|NumeralType   $dayOfQuarter
     * @param null|int|NumeralType   $hour
     * @param null|int|NumeralType   $minute
     * @param null|int|NumeralType   $second
     * @param null|int|NumeralType   $millisecond
     * @param null|int|NumeralType   $microsecond
     * @param null|int|NumeralType   $nanosecond
     * @param null|string|StringType $timezone
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
        return Procedure::datetime(
            self::makeTemporalMap(
                [
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
                ]
            )
        );
    }

    /**
     * Creates a datetime with the specified year, ordinalDay, hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType        $year
     * @param null|int|NumeralType   $ordinalDay
     * @param null|int|NumeralType   $hour
     * @param null|int|NumeralType   $minute
     * @param null|int|NumeralType   $second
     * @param null|int|NumeralType   $millisecond
     * @param null|int|NumeralType   $microsecond
     * @param null|int|NumeralType   $nanosecond
     * @param null|string|StringType $timezone
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
        return Procedure::datetime(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "ordinalDay" => $ordinalDay,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                    "timezone"   => $timezone,
                ]
            )
        );
    }

    /**
     * Creates a datetime by parsing a string representation of a temporal value.
     *
     * @param string|StringType $dateString
     */
    public static function dateTimeString($dateString): DateTime
    {
        return Procedure::datetime(self::toStringType($dateString));
    }

    /**
     * Creates the current localDateTime value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-current
     */
    public static function localDateTime($timezone = null): LocalDateTime
    {
        if ($timezone === null) {
            return Procedure::localdatetime();
        }

        return Procedure::localdatetime(Query::map(["timezone" => self::toStringType($timezone)]));
    }

    /**
     * Creates a LocalDateTime value with specified year, month, day and time props.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
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
        return Procedure::localdatetime(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "month" => $month,
                    "day" => $day,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                ]
            )
        );
    }

    /**
     * Creates a LocalDateTime value with the specified year, week, dayOfWeek, hour, minute,
     * second, millisecond, microsecond and nanosecond component value.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $dayOfWeek
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
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
        return Procedure::localdatetime(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "week" => $week,
                    "dayOfWeek" => $dayOfWeek,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                ]
            )
        );
    }

    /**
     * Creates a LocalDateTime value with the specified year, quarter, dayOfQuarter, hour, minute, second, millisecond, microsecond and nanosecond component values.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $quarter
     * @param null|int|NumeralType $dayOfQuarter
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
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
        return Procedure::localdatetime(
            self::MakeTemporalMap(
                [
                    "year" => $year,
                    "quarter" => $quarter,
                    "dayOfQuarter" => $dayOfQuarter,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                ]
            )
        );
    }

    /**
     * Creates a LocalDateTime value with the specified year, ordinalDay, hour, minute, second, millisecond, microsecond and nanosecond component values.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $ordinalDay
     * @param null|int|NumeralType $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
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
        return Procedure::localdatetime(
            self::makeTemporalMap(
                [
                    "year" => $year,
                    "ordinalDay" => $ordinalDay,
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                ]
            )
        );
    }

    /**
     * Creates the LocalDateTime value obtained by parsing a string representation of a temporal value.
     *
     * @param string|StringType $localDateTimeString
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-create-string
     */
    public static function localDateTimeString($localDateTimeString): LocalDateTime
    {
        return Procedure::localdatetime(self::toStringType($localDateTimeString));
    }

    /**
     * Creates the current LocalTime value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-current
     */
    public static function localTimeCurrent($timezone = null): LocalTime
    {
        if ($timezone === null) {
            return Procedure::localtime();
        }

        return Procedure::localtime(Query::map(["timezone" => self::toStringType($timezone)]));
    }

    /**
     * Creates a LocalTime value with the specified hour, minute, second, millisecond, microsecond and nanosecond component values.
     *
     * @param int|NumeralType      $hour
     * @param null|int|NumeralType $minute
     * @param null|int|NumeralType $second
     * @param null|int|NumeralType $millisecond
     * @param null|int|NumeralType $microsecond
     * @param null|int|NumeralType $nanosecond
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
        return Procedure::localtime(
            self::makeTemporalMap(
                [
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                ]
            )
        );
    }

    /**
     * Creates the LocalTime value obtained by parsing a string representation of a temporal value.
     *
     * @param string|StringType $localTimeString
     */
    public static function localTimeString($localTimeString): LocalTime
    {
        return Procedure::localtime(self::toStringType($localTimeString));
    }

    /**
     * Creates the current Time value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-current
     */
    public static function time($timezone = null): Time
    {
        if ($timezone === null) {
            return Procedure::time();
        }

        return Procedure::time(Query::map(["timezone" => self::toStringType($timezone)]));
    }

    /**
     * Creates  a Time value with the specified hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @param int|NumeralType        $hour
     * @param null|int|NumeralType   $minute
     * @param null|int|NumeralType   $second
     * @param null|int|NumeralType   $millisecond
     * @param null|int|NumeralType   $microsecond
     * @param null|int|NumeralType   $nanosecond
     * @param null|string|StringType $timezone
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
        return Procedure::time(
            self::makeTemporalMap(
                [
                    "hour" => $hour,
                    "minute" => $minute,
                    "second" => $second,
                    "millisecond" => $millisecond,
                    "microsecond" => $microsecond,
                    "nanosecond" => $nanosecond,
                    "timezone" => $timezone,
                ]
            )
        );
    }

    /**
     * Creates the Time value obtained by parsing a string representation of a temporal value.
     *
     * @param string|StringType $timeString
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create-string
     */
    public static function timeString($timeString): Time
    {
        return Procedure::time(self::toStringType($timeString));
    }

    /**
     * Creates a 2d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-2d
     */
    public static function point2d($x, $y): Point
    {
        $map = [
            "x" => self::toNumeralType($x),
            "y" => self::toNumeralType($y),
        ];

        $map["crs"] = self::string("cartesian");

        return Procedure::point(Query::map($map));
    }

    /**
     * Creates a 3d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @param float|int|NumeralType $z
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-3d
     */
    public static function point3d($x, $y, $z): Point
    {
        $map = [
            "x" => self::toNumeralType($x),
            "y" => self::toNumeralType($y),
            "z" => self::toNumeralType($z),
        ];

        $map["crs"] = self::string("cartesian-3D");

        return Procedure::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point2dWGS84($longitude, $latitude): Point
    {
        $map = [
            "longitude" => self::toNumeralType($longitude),
            "latitude" => self::toNumeralType($latitude),
        ];

        $map["crs"] = self::string("WGS-84");

        return Procedure::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @param float|int|NumeralType $height
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point3dWGS84($longitude, $latitude, $height): Point
    {
        $map = [
            "longitude" => self::toNumeralType($longitude),
            "latitude" => self::toNumeralType($latitude),
            "height" => self::toNumeralType($height),
        ];

        $map["crs"] = self::string("WGS-84-3D");

        return Procedure::point(Query::map($map));
    }

    /**
     * Prevent the construction of this class by making the constructor private.
     */
    private function __construct()
    {
    }

    /**
     * Prepares the variables to be used by temporal (i.e. time-like) CYPHER-functions.
     *
     * The following are done:
     * - For all $variables except for the 'timezone' it is checked if any one of them exists without the previous variable existing, up to & including the 'second' variable.
     * - If a 'second' variable is encountered, it is checked if 'seconds' is not-null when milliseconds/microseconds/nanoseconds are provided.
     * - All variables except 'timezone' are made into NumeralType.
     * - 'timezone' is made into StringLiteral.
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
                $map[$key] = self::toStringType($variable);
            } else {
                if (!$secondsFound && $nullEncountered) {
                    // Check if none of the previous, i.e. more important components, are null.
                    // sub-second values are not interdependent, but seconds must then be provided.
                    throw new LogicException("The key {$key} can only be provided when all more significant components are provided as well.");
                }

                if ($key === 'second') {
                    $secondsFound = true;
                }

                $map[$key] = self::toNumeralType($variable);
            }
        }

        return Query::map($map);
    }
}
