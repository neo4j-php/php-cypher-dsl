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

namespace WikibaseSolutions\CypherDSL\Literals;

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Functions\FunctionCall;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * Helper class to construct literals.
 *
 * @package WikibaseSolutions\CypherDSL\Literals
 */
abstract class Literal
{
    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * @param mixed $literal The literal to construct
     * @return StringLiteral|Boolean|Decimal
     */
    public static function literal($literal): PropertyType
    {
        if (is_string($literal) || (is_object($literal) && method_exists($literal, '__toString'))) {
            return self::string($literal);
        }

        if (is_bool($literal)) {
            return self::boolean($literal);
        }

        if (is_int($literal) || is_float($literal)) {
            return self::decimal($literal);
        }

        $actualType = is_object($literal) ? get_class($literal) : gettype($literal);

        throw new InvalidArgumentException("The literal type " . $actualType . " is not supported by Cypher");
    }

    /**
     * Creates a new boolean.
     *
     * @param bool $value
     * @return BooleanType
     */
    public static function boolean(bool $value): BooleanType
    {
        return new Boolean($value);
    }

    /**
     * Creates a new string.
     *
     * @param string $value
     * @return StringLiteral
     */
    public static function string(string $value): StringType
    {
        return new StringLiteral($value);
    }

    /**
     * Creates a new decimal literal.
     *
     * @param int|float $value
     * @return Decimal
     */
    public static function decimal($value): NumeralType
    {
        return new Decimal($value);
    }

    /**
     * Retrieves the current Date value, optionally for a different time zone. In reality, this function just returns
     * a call to the "date()" function.
     *
     * @param string|StringType $timezone
     * @return DateType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-current
     */
    public static function date($timezone = null): DateType
    {
        if ($timezone === null) {
            return FunctionCall::date();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }

        return FunctionCall::date(Query::map(["timezone" => $timezone]));
    }

    /**
     * Creates a date from the given year, month and day.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $month
     * @param null|int|NumeralType $day
     * @return DateType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-calendar
     */
    public static function dateYMD($year, $month = null, $day = null): DateType
    {
        if ($month === null && $day !== null) {
            throw new \LogicException("If \$month is omitted, \$day must also be omitted");
        }

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($month !== null) {
            if (!($month instanceof NumeralType)) {
                $month = self::decimal($month);
            }

            $map["month"] = $month;
        }

        if ($day !== null) {
            if (!($day instanceof NumeralType)) {
                $day = self::decimal($day);
            }

            $map["day"] = $day;
        }

        return FunctionCall::date(Query::map($map));
    }

    /**
     * Creates a date from the given year, week and weekday.
     *
     * @param int|NumeralType $year
     * @param null|int|NumeralType $week
     * @param null|int|NumeralType $weekday
     * @return DateType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-week
     */
    public static function dateYWD($year, $week = null, $weekday = null): DateType
    {
        if ($week === null && $weekday !== null) {
            throw new \LogicException("If \$week is omitted, \$weekday must also be omitted");
        }

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($week !== null) {
            if (!($week instanceof NumeralType)) {
                $week = self::decimal($week);
            }

            $map["week"] = $week;
        }

        if ($weekday !== null) {
            if (!($weekday instanceof NumeralType)) {
                $weekday = self::decimal($weekday);
            }

            $map["dayOfWeek"] = $weekday;
        }

        return FunctionCall::date(Query::map($map));
    }

    /**
     * Creates a date from the given string.
     *
     * @param string|StringLiteral $date
     * @return DateType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date-create-string
     */
    public static function dateString($date): DateType
    {
        if (!($date instanceof StringLiteral)) {
            $date = self::string($date);
        }

        return FunctionCall::date($date);
    }

    /**
     * Retrieves the current DateTime value, optionally for a different time zone. In reality, this
     * function just returns a call to the "datetime()" function.
     *
     * @param string|StringType $timezone
     * @return DateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-current
     */
    public static function dateTime($timezone = null): DateTimeType
    {
        if ($timezone === null) {
            return FunctionCall::datetime();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }

        return FunctionCall::datetime(Query::map(["timezone" => $timezone]));
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
     * @return DateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-calendar
     */
    public static function dateTimeYMD($year, $month = null, $day = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null, $timezone = null): DateTimeType
    {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "month"=> $month,
            "day" => $day,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($month !== null) $map["month"] = $setVariables["month"];
        if ($day !== null) $map["day"] = $setVariables["day"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];
        if ($timezone !== null) {
            if (!($timezone instanceof StringType)) {
                $timezone = self::string($timezone);
            }
            $map["timezone"] = $timezone;
        }

        return FunctionCall::datetime(Query::map($map));
    }

    /**
     * checks the order of the array items, and converts the items to NumeralType
     *
     * @param array $array
     * @return array
     */
    private static function checkOrderAndConvert2Numeral(array $array): array {
        $previous = true;

        foreach ($array as $key => $setVariable) {
            // Only the least significant components may be omitted; check whether this is the case
            if (isset($setVariable) === true && $previous === false) {
                throw new \LogicException("Only the least significant components may be omitted");
            }

            // check if variable has been set and convert always to NumeralType
            if (isset($setVariable)) {
                if (!($setVariable instanceof NumeralType)) {
                    $setVariable = self::decimal($setVariable);
                    $array[$key] = $setVariable;
                }
            }

            $previous = isset($setVariable);
        }

        return $array;
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
     * @return DateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-week
     */
    public static function datetimeYWD($year, $week = null, $dayOfWeek = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null, $timezone = null): DateTimeType
    {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "week" => $week,
            "dayOfWeek" => $dayOfWeek,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($week !== null) $map["week"] = $setVariables["week"];
        if ($dayOfWeek !== null) $map["dayOfWeek"] = $setVariables["dayOfWeek"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];
        if ($timezone !== null) {
            if (!($timezone instanceof StringType)) {
                $timezone = self::string($timezone);
            }
            $map["timezone"] = $timezone;
        }

        return FunctionCall::datetime(Query::map($map));
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
     * @return DateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-quarter
     */
    public static function datetimeYQD($year, $quarter = null, $dayOfQuarter = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null, $timezone = null): DateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "quarter" => $quarter,
            "dayOfQuarter" => $dayOfQuarter,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($quarter !== null) $map["quarter"] = $setVariables["quarter"];
        if ($dayOfQuarter !== null) $map["dayOfQuarter"] = $setVariables["dayOfQuarter"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];
        if ($timezone !== null) {
            if (!($timezone instanceof StringType)) {
                $timezone = self::string($timezone);
            }
            $map["timezone"] = $timezone;
        }

        return FunctionCall::datetime(Query::map($map));
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
     * @return DateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime-ordinal
     */
    public static function datetimeYD($year, $ordinalDay = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null, $timezone = null): DateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "ordinalDay" => $ordinalDay,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($ordinalDay !== null) $map["ordinalDay"] = $setVariables["ordinalDay"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];
        if ($timezone !== null) {
            if (!($timezone instanceof StringType)) {
                $timezone = self::string($timezone);
            }
            $map["timezone"] = $timezone;
        }

        return FunctionCall::datetime(Query::map($map));
    }

    /**
     * Creates a datetime by parsing a string representation of a temporal value
     *
     * @param string|StringType $dateString
     * @return DateTimeType
     */
    public static function datetimeString($dateString): DateTimeType {
        if (!($dateString instanceof StringType)) {
            $dateString = self::string($dateString);
        }
        return FunctionCall::datetime($dateString);
    }

    /**
     * Creates the current localDateTime value
     *
     * @param null|string|StringType $timezone
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-current
     */
    public static function localDatetime($timezone = null): LocalDateTimeType {
        if ($timezone === null) {
            return FunctionCall::localdatetime();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }
        return FunctionCall::localdatetime(Query::map(["timezone" => $timezone]));
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
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-calendar
     */
    public static function localDatetimeYMD($year, $month = null, $day = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null): LocalDateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "month" => $month,
            "day" => $day,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($month !== null) $map["month"] = $setVariables["month"];
        if ($day !== null) $map["day"] = $setVariables["day"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];

        return FunctionCall::localdatetime(Query::map($map));
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
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-week
     */
    public static function localDatetimeYWD($year, $week = null, $dayOfWeek = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null): LocalDateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "week" => $week,
            "dayOfWeek" => $dayOfWeek,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($week !== null) $map["week"] = $setVariables["week"];
        if ($dayOfWeek !== null) $map["dayOfWeek"] = $setVariables["dayOfWeek"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];

        return FunctionCall::localdatetime(Query::map($map));
    }

    /**
     * Creates a LocalDateTime value with the specified year, quarter, dayOfQuarter, hour, minute, second, millisecond, microsecond and nanosecond component values
     *
     * @param $year
     * @param null $quarter
     * @param null $dayOfQuarter
     * @param null $hour
     * @param null $minute
     * @param null $second
     * @param null $millisecond
     * @param null $microsecond
     * @param null $nanosecond
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-quarter
     */
    public static function localDatetimeYQD($year, $quarter = null, $dayOfQuarter = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null): LocalDateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "quarter" => $quarter,
            "dayOfQuarter" => $dayOfQuarter,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($quarter !== null) $map["quarter"] = $setVariables["quarter"];
        if ($dayOfQuarter !== null) $map["dayOfQuarter"] = $setVariables["dayOfQuarter"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];

        return FunctionCall::localdatetime(Query::map($map));
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
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-ordinal
     */
    public static function localDatetimeYD($year, $ordinalDay = null, $hour = null, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null): LocalDateTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "ordinalDay" => $ordinalDay,
            "hour" => $hour,
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($year instanceof NumeralType)) {
            $year = self::decimal($year);
        }

        $map = ["year" => $year];

        if ($ordinalDay !== null) $map["ordinalDay"] = $setVariables["ordinalDay"];
        if ($hour !== null) $map["hour"] = $setVariables["hour"];
        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];

        return FunctionCall::localdatetime(Query::map($map));
    }

    /**
     * Creates the LocalDateTime value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $localDateTimeString
     * @return LocalDateTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localdatetime-create-string
     */
    public static function localDatetimeString($localDateTimeString): LocalDateTimeType {
        if (!($localDateTimeString instanceof StringType)) {
            $localDateTimeString = self::string($localDateTimeString);
        }

        return FunctionCall::localdatetime($localDateTimeString);
    }

    /**
     * Creates the current LocalTime value
     *
     * @param null|string|StringType $timezone
     * @return LocalTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-current
     */
    public static function localTimeCurrent($timezone = null): LocalTimeType {
        if ($timezone === null) {
            return FunctionCall::localtime();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }
        return FunctionCall::localtime(Query::map(["timezone" => $timezone]));
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
     * @return LocalTimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime-create
     */
    public static function localTime($hour, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null): LocalTimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($hour instanceof NumeralType)) {
            $hour = self::decimal($hour);
        }

        $map = ["hour" => $hour];

        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];

        return FunctionCall::localtime(Query::map($map));
    }

    /**
     * Creates the LocalTime value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $localTimeString
     * @return LocalTimeType
     */
    public static function localTimeString($localTimeString): LocalTimeType {
        if (!($localTimeString instanceof StringType)) {
            $localTimeString = self::string($localTimeString);
        }

        return FunctionCall::localtime($localTimeString);
    }

    /**
     * Creates the current Time value
     *
     * @param null|string|StringType $timezone
     * @return TimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-current
     */
    public static function timeCurrent($timezone = null): TimeType {
        if ($timezone === null) {
            return FunctionCall::time();
        }

        if (!($timezone instanceof StringType)) {
            $timezone = self::string($timezone);
        }
        return FunctionCall::time(Query::map(["timezone" => $timezone]));
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
     * @return TimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create
     */
    public static function time($hour, $minute = null, $second = null, $millisecond = null, $microsecond = null, $nanosecond = null, $timezone = null): TimeType {
        $setVariables = self::checkOrderAndConvert2Numeral([
            "minute" => $minute,
            "second" => $second,
            "millisecond" => $millisecond,
            "microsecond" => $microsecond,
            "nanosecond" => $nanosecond
        ]);

        if (!($hour instanceof NumeralType)) {
            $hour = self::decimal($hour);
        }

        $map = ["hour" => $hour];

        if ($minute !== null) $map["minute"] = $setVariables["minute"];
        if ($second !== null) $map["second"] = $setVariables["second"];
        if ($millisecond !== null) $map["millisecond"] = $setVariables["millisecond"];
        if ($microsecond !== null) $map["microsecond"] = $setVariables["microsecond"];
        if ($nanosecond !== null) $map["nanosecond"] = $setVariables["nanosecond"];
        if ($timezone !== null) {
            if (!($timezone instanceof StringType)) {
                $timezone = self::string($timezone);
            }
            $map["timezone"] = $timezone;
        }

        return FunctionCall::time(Query::map($map));
    }

    /**
     * Creates the Time value obtained by parsing a string representation of a temporal value
     *
     * @param string|StringType $timeString
     * @return TimeType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time-create-string
     */
    public static function timeString($timeString): TimeType {
        if (!($timeString instanceof StringType)) {
            $timeString = self::string($timeString);
        }
        return FunctionCall::time($timeString);
    }

    /**
     * Creates a 2d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @return PointType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-2d
     */
    public static function point2d($x, $y): PointType
    {
        if (!($x instanceof NumeralType)) {
            $x = self::decimal($x);
        }

        if (!($y instanceof NumeralType)) {
            $y = self::decimal($y);
        }

        $map = [
            "x" => $x,
            "y" => $y
        ];

        $map["crs"] = self::string("cartesian");
        $map["srid"] = self::decimal(7203);

        return FunctionCall::point(Query::map($map));
    }

    /**
     * Creates a 3d cartesian point.
     *
     * @param float|int|NumeralType $x
     * @param float|int|NumeralType $y
     * @param float|int|NumeralType $z
     * @return PointType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-cartesian-3d
     */
    public static function point3d($x, $y, $z): PointType
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
            "z" => $z
        ];

        $map["crs"] = self::string("cartesian-3D");
        $map["srid"] = self::decimal(9157);

        return FunctionCall::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @return PointType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point2dWGS84($longitude, $latitude): PointType
    {
        if (!($longitude instanceof NumeralType)) {
            $longitude = self::decimal($longitude);
        }

        if (!($latitude instanceof NumeralType)) {
            $latitude = self::decimal($latitude);
        }

        $map = [
            "longitude" => $longitude,
            "latitude" => $latitude
        ];

        $map["crs"] = self::string("WGS-84");
        $map["srid"] = self::decimal(4326);

        return FunctionCall::point(Query::map($map));
    }

    /**
     * Creates a WGS 84 2D point.
     *
     * @param float|int|NumeralType $longitude
     * @param float|int|NumeralType $latitude
     * @param float|int|NumeralType $height
     * @return PointType
     *
     * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/#functions-point-wgs84-2d
     */
    public static function point3dWGS84($longitude, $latitude, $height): PointType
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
            "height" => $height
        ];

        $map["crs"] = self::string("WGS-84-3D");
        $map["srid"] = self::decimal(4979);

        return FunctionCall::point(Query::map($map));
    }
}