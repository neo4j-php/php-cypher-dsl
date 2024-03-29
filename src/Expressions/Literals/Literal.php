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
            // @phpstan-ignore-next-line
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

        // @phpstan-ignore-next-line PHPStan does not work well with classes named "Integer"
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
     * @param mixed[] $value
     */
    public static function list(iterable $value): List_
    {
        if ($value instanceof Traversable) {
            $value = iterator_to_array($value);
        }

        return new List_($value);
    }

    /**
     * Creates a new map literal.
     *
     * @param mixed[] $value
     */
    public static function map(array $value): Map
    {
        return new Map($value);
    }

    /**
     * Retrieves the current Date value, optionally for a different time zone. In reality, this function just returns
     * a call to the "date()" function.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-current Corresponding documentation on Neo4j.com
     */
    public static function date($timezone = null): Date
    {
        if ($timezone === null) {
            return Procedure::date();
        }

        return Procedure::date(["timezone" => $timezone]);
    }

    /**
     * Creates a date from the given year, month and day.
     *
     * @param int|NumeralType      $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-calendar Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-week Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-create-string Corresponding documentation on Neo4j.com
     */
    public static function dateString($date): Date
    {
        return Procedure::date($date);
    }

    /**
     * Retrieves the current DateTime value, optionally for a different time zone. In reality, this
     * function just returns a call to the "datetime()" function.
     *
     * @param string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-current Corresponding documentation on Neo4j.com
     */
    public static function dateTime($timezone = null): DateTime
    {
        if ($timezone === null) {
            return Procedure::datetime();
        }

        return Procedure::datetime(["timezone" => $timezone]);
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-calendar Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-week Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-quarter Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-ordinal Corresponding documentation on Neo4j.com
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
        return Procedure::datetime($dateString);
    }

    /**
     * Creates the current localDateTime value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-current Corresponding documentation on Neo4j.com
     */
    public static function localDateTime($timezone = null): LocalDateTime
    {
        if ($timezone === null) {
            return Procedure::localdatetime();
        }

        return Procedure::localdatetime(["timezone" => $timezone]);
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-calendar Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-week Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-quarter Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-ordinal Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-create-string Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeString($localDateTimeString): LocalDateTime
    {
        return Procedure::localdatetime($localDateTimeString);
    }

    /**
     * Creates the current LocalTime value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-current Corresponding documentation on Neo4j.com
     */
    public static function localTimeCurrent($timezone = null): LocalTime
    {
        if ($timezone === null) {
            return Procedure::localtime();
        }

        return Procedure::localtime(["timezone" => $timezone]);
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-create Corresponding documentation on Neo4j.com
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
        return Procedure::localtime($localTimeString);
    }

    /**
     * Creates the current Time value.
     *
     * @param null|string|StringType $timezone
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-current Corresponding documentation on Neo4j.com
     */
    public static function time($timezone = null): Time
    {
        if ($timezone === null) {
            return Procedure::time();
        }

        return Procedure::time(["timezone" => $timezone]);
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create Corresponding documentation on Neo4j.com
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create-string Corresponding documentation on Neo4j.com
     */
    public static function timeString($timeString): Time
    {
        return Procedure::time($timeString);
    }

    /**
     * Creates a 2d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-2d Corresponding documentation on Neo4j.com
     */
    public static function point2d($x, $y): Point
    {
        return Procedure::point([
            "x" => $x,
            "y" => $y,
            "crs" => "cartesian",
        ]);
    }

    /**
     * Creates a 3d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @param float|int|NumeralType $z
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-3d Corresponding documentation on Neo4j.com
     */
    public static function point3d($x, $y, $z): Point
    {
        return Procedure::point([
            "x" => $x,
            "y" => $y,
            "z" => $z,
            "crs" => "cartesian-3D",
        ]);
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d Corresponding documentation on Neo4j.com
     */
    public static function point2dWGS84($longitude, $latitude): Point
    {
        return Procedure::point([
            "longitude" => $longitude,
            "latitude" => $latitude,
            "crs" => "WGS-84",
        ]);
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @param float|int|NumeralType $height
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d Corresponding documentation on Neo4j.com
     */
    public static function point3dWGS84($longitude, $latitude, $height): Point
    {
        return Procedure::point([
            "longitude" => $longitude,
            "latitude" => $latitude,
            "height" => $height,
            "crs" => "WGS-84-3D",
        ]);
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
     * @param mixed[] $variables
     *
     * @return mixed[]
     */
    private static function makeTemporalMap(array $variables): array
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
                $map[$key] = $variable;
            } else {
                if (!$secondsFound && $nullEncountered) {
                    // Check if none of the previous, i.e. more important components, are null.
                    // sub-second values are not interdependent, but seconds must then be provided.
                    throw new LogicException("The key {$key} can only be provided when all more significant components are provided as well.");
                }

                if ($key === 'second') {
                    $secondsFound = true;
                }

                $map[$key] = $variable;
            }
        }

        return $map;
    }
}
