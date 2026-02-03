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
use Stringable;
use Traversable;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Helper class to construct literals.
 *
 * @note This class should only contain static methods.
 */
final class Literal
{
    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * @param array|bool|float|int|string|Stringable $literal The literal to construct
     *
     * @throws TypeError when the type could not be deduced
     */
    public static function literal(string|bool|int|float|array|Stringable $literal): Boolean|Float_|Integer|List_|Map|String_
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

        return self::string($literal->__toString());
    }

    /**
     * Creates a new numeral literal.
     *
     * @see        Literal::number()
     * @deprecated
     */
    public static function decimal(float|int $value): Float_|Integer
    {
        return self::number($value);
    }

    /**
     * Creates a new numeral literal.
     */
    public static function number(float|int $value): Float_|Integer
    {
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
     */
    public static function map(array $value): Map
    {
        return new Map($value);
    }

    /**
     * Retrieves the current Date value, optionally for a different time zone. In reality, this function just returns
     * a call to the "date()" function.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-current Corresponding documentation on Neo4j.com
     */
    public static function date(StringType|string|null $timezone = null): Date
    {
        if ($timezone === null) {
            return Procedure::date();
        }

        return Procedure::date(["timezone" => $timezone]);
    }

    /**
     * Creates a date from the given year, month and day.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-calendar Corresponding documentation on Neo4j.com
     */
    public static function dateYMD(NumeralType|int $year, NumeralType|int|null $month = null, NumeralType|int|null $day = null): Date
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-week Corresponding documentation on Neo4j.com
     */
    public static function dateYWD(NumeralType|int $year, NumeralType|int|null $week = null, NumeralType|int|null $weekday = null): Date
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-create-string Corresponding documentation on Neo4j.com
     */
    public static function dateString(StringType|string $date): Date
    {
        return Procedure::date($date);
    }

    /**
     * Retrieves the current DateTime value, optionally for a different time zone. In reality, this
     * function just returns a call to the "datetime()" function.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-current Corresponding documentation on Neo4j.com
     */
    public static function dateTime(StringType|string|null $timezone = null): DateTime
    {
        if ($timezone === null) {
            return Procedure::datetime();
        }

        return Procedure::datetime(["timezone" => $timezone]);
    }

    /**
     * Creates a date from the given year, month, day and time values.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-calendar Corresponding documentation on Neo4j.com
     */
    public static function dateTimeYMD(
        NumeralType|int $year,
        NumeralType|int|null $month = null,
        NumeralType|int|null $day = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null,
        StringType|string|null $timezone = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-week Corresponding documentation on Neo4j.com
     */
    public static function dateTimeYWD(
        NumeralType|int $year,
        NumeralType|int|null $week = null,
        NumeralType|int|null $dayOfWeek = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null,
        StringType|string|null $timezone = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-quarter Corresponding documentation on Neo4j.com
     */
    public static function dateTimeYQD(
        NumeralType|int $year,
        NumeralType|int|null $quarter = null,
        NumeralType|int|null $dayOfQuarter = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null,
        StringType|string|null $timezone = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-ordinal Corresponding documentation on Neo4j.com
     */
    public static function dateTimeYD(
        NumeralType|int $year,
        NumeralType|int|null $ordinalDay = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null,
        StringType|string|null $timezone = null
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
     */
    public static function dateTimeString(StringType|string $dateString): DateTime
    {
        return Procedure::datetime($dateString);
    }

    /**
     * Creates the current localDateTime value.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-current Corresponding documentation on Neo4j.com
     */
    public static function localDateTime(StringType|string|null $timezone = null): LocalDateTime
    {
        if ($timezone === null) {
            return Procedure::localdatetime();
        }

        return Procedure::localdatetime(["timezone" => $timezone]);
    }

    /**
     * Creates a LocalDateTime value with specified year, month, day and time props.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-calendar Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeYMD(
        NumeralType|int $year,
        NumeralType|int|null $month = null,
        NumeralType|int|null $day = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-week Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeYWD(
        NumeralType|int $year,
        NumeralType|int|null $week = null,
        NumeralType|int|null $dayOfWeek = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-quarter Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeYQD(
        NumeralType|int $year,
        NumeralType|int|null $quarter = null,
        NumeralType|int|null $dayOfQuarter = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-ordinal Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeYD(
        NumeralType|int $year,
        NumeralType|int|null $ordinalDay = null,
        NumeralType|int|null $hour = null,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-create-string Corresponding documentation on Neo4j.com
     */
    public static function localDateTimeString(StringType|string $localDateTimeString): LocalDateTime
    {
        return Procedure::localdatetime($localDateTimeString);
    }

    /**
     * Creates the current LocalTime value.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-current Corresponding documentation on Neo4j.com
     */
    public static function localTimeCurrent(StringType|string|null $timezone = null): LocalTime
    {
        if ($timezone === null) {
            return Procedure::localtime();
        }

        return Procedure::localtime(["timezone" => $timezone]);
    }

    /**
     * Creates a LocalTime value with the specified hour, minute, second, millisecond, microsecond and nanosecond component values.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-create Corresponding documentation on Neo4j.com
     */
    public static function localTime(
        NumeralType|int $hour,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null
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
     */
    public static function localTimeString(StringType|string $localTimeString): LocalTime
    {
        return Procedure::localtime($localTimeString);
    }

    /**
     * Creates the current Time value.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-current Corresponding documentation on Neo4j.com
     */
    public static function time(StringType|string|null $timezone = null): Time
    {
        if ($timezone === null) {
            return Procedure::time();
        }

        return Procedure::time(["timezone" => $timezone]);
    }

    /**
     * Creates  a Time value with the specified hour, minute, second, millisecond, microsecond, nanosecond and timezone component values.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create Corresponding documentation on Neo4j.com
     */
    public static function timeHMS(
        NumeralType|int $hour,
        NumeralType|int|null $minute = null,
        NumeralType|int|null $second = null,
        NumeralType|int|null $millisecond = null,
        NumeralType|int|null $microsecond = null,
        NumeralType|int|null $nanosecond = null,
        StringType|string|null $timezone = null
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create-string Corresponding documentation on Neo4j.com
     */
    public static function timeString(StringType|string $timeString): Time
    {
        return Procedure::time($timeString);
    }

    /**
     * Creates a 2d cartesian point.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-2d Corresponding documentation on Neo4j.com
     */
    public static function point2d(NumeralType|int|float $x, NumeralType|int|float $y): Point
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-3d Corresponding documentation on Neo4j.com
     */
    public static function point3d(NumeralType|int|float $x, NumeralType|int|float $y, NumeralType|int|float $z): Point
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d Corresponding documentation on Neo4j.com
     */
    public static function point2dWGS84(NumeralType|int|float $longitude, NumeralType|int|float $latitude): Point
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
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d Corresponding documentation on Neo4j.com
     */
    public static function point3dWGS84(NumeralType|int|float $longitude, NumeralType|int|float $latitude, NumeralType|int|float $height): Point
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
     * @return (AnyType|array|bool|float|int|Pattern|string)[]
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
