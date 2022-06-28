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

namespace WikibaseSolutions\CypherDSL\Functions;

use WikibaseSolutions\CypherDSL\QueryConvertable;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\HelperTraits\AliasableTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * This class represents any function call.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/
 */
abstract class FunctionCall implements QueryConvertable
{
    use AliasableTrait;

    /**
     * Produces a raw function call. This enables the usage of unimplemented functions in your
     * Cypher queries. The parameters of this function are not type-checked.
     *
     * @param string $functionName The name of the function to call
     * @param AnyType[] $parameters The parameters to pass to the function call
     *
     * @return RawFunction
     */
    public static function raw(string $functionName, array $parameters): RawFunction
    {
        return new RawFunction($functionName, $parameters);
    }

    /**
     * Calls the "all()" function. The signature of the "all()" function is:
     *
     * all(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param Variable $variable A variable that can be used from within the predicate
     * @param ListType $list A list
     * @param AnyType $predicate A predicate that is tested against all items in the list
     *
     * @return All
     */
    public static function all(Variable $variable, ListType $list, AnyType $predicate): All
    {
        return new All($variable, $list, $predicate);
    }

    /**
     * Calls the "any()" function. The signature of the "any()" function is:
     *
     * any(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param Variable $variable A variable that can be used from within the predicate
     * @param ListType $list A list
     * @param AnyType $predicate A predicate that is tested against all items in the list
     *
     * @return Any
     */
    public static function any(Variable $variable, ListType $list, AnyType $predicate): Any
    {
        return new Any($variable, $list, $predicate);
    }

    /**
     * Calls the "exists()" function. The signature of the "exists()" function is:
     *
     * exists(input :: ANY?) :: (BOOLEAN?)
     *
     * @param AnyType $expression A pattern or property
     *
     * @return Exists
     */
    public static function exists(AnyType $expression): Exists
    {
        return new Exists($expression);
    }

    /**
     * Calls the "isEmpty()" function. The signature of the "isEmpty()" function is:
     *
     * isEmpty(input :: LIST? OF ANY?) :: (BOOLEAN?) - to check whether a list is empty
     * isEmpty(input :: MAP?) :: (BOOLEAN?) - to check whether a map is empty
     * isEmpty(input :: STRING?) :: (BOOLEAN?) - to check whether a string is empty
     *
     * @param ListType|MapType|StringType $list An expression that returns a list
     *
     * @return IsEmpty
     */
    public static function isEmpty(AnyType $list): IsEmpty
    {
        return new IsEmpty($list);
    }

    /**
     * Calls the "none()" function. The signature of the "none()" function is:
     *
     * none(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param Variable $variable A variable that can be used from within the predicate
     * @param ListType $list A list
     * @param AnyType $predicate A predicate that is tested against all items in the list
     *
     * @return None
     */
    public static function none(Variable $variable, ListType $list, AnyType $predicate): None
    {
        return new None($variable, $list, $predicate);
    }

    /**
     * Calls the "single()" function. The signature of the "single()" function is:
     *
     * single(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param Variable $variable A variable that can be used from within the predicate
     * @param ListType $list A list
     * @param AnyType $predicate A predicate that is tested against all items in the list
     *
     * @return Single
     */
    public static function single(Variable $variable, ListType $list, AnyType $predicate): Single
    {
        return new Single($variable, $list, $predicate);
    }

    /**
     * Calls the "point()" function. The signature of the "point()" function is:
     *
     * point(input :: MAP?) :: (POINT?)
     *
     * @param MapType $map The map to use for constructing the point
     * @note You probably want to use the Literal class instead of this function
     *
     * @return Point
     */
    public static function point(MapType $map): Point
    {
        return new Point($map);
    }

    /**
     * Calls the "date()" function. The signature of the "date()" function is:
     *
     * date(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATE?)
     *
     * @param AnyType|null $value The input to the date function, from which to construct the date
     * @note You probably want to use the Literal class instead of this function
     *
     * @return Date
     */
    public static function date(?AnyType $value = null): Date
    {
        return new Date($value);
    }

    /**
     * Calls the "datetime()" function. The signature of the "datetime()" function is:
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATETIME?)
     *
     * @param AnyType|null $value The input to the datetime function, from which to construct the datetime
     * @note You probably want to use the Literal class instead of this function
     *
     * @return DateTime
     */
    public static function datetime(?AnyType $value = null): DateTime
    {
        return new DateTime($value);
    }

    /**
     * Calls the "localdatetime()" function. The signature of the "localdatetime()" function is:
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (LOCALDATETIME?)
     *
     * @param AnyType|null $value The input to the localdatetime function, from which to construct the localdatetime
     * @note You probably want to use the Literal class instead of this function
     *
     * @return LocalDateTime
     */
    public static function localdatetime(?AnyType $value = null): LocalDateTime
    {
        return new LocalDateTime($value);
    }

    /**
     * Calls the "localtime()" function. The signature of the "localtime()" function is:
     *
     * localtime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (LOCALTIME?)
     *
     * @param AnyType|null $value The input to the localtime function, from which to construct the localtime
     * @note You probably want to use the Literal class instead of this function
     *
     * @return LocalTime
     */
    public static function localtime(?AnyType $value = null): LocalTime
    {
        return new LocalTime($value);
    }

    /**
     * Calls the "time()" function. The signature of the "time()" function is:
     *
     * time(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (TIME?)
     *
     * @param AnyType|null $value The input to the localtime function, from which to construct the time
     * @note You probably want to use the Literal class instead of this function
     *
     * @return Time
     */
    public static function time(?AnyType $value = null): Time
    {
        return new Time($value);
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $signature = $this->getSignature();
        $parameters = array_map(
            fn (QueryConvertable $convertable): string => $convertable->toQuery(),
            $this->getParameters()
        );

        return sprintf($signature, ...$parameters);
    }

    /**
     * Returns the signature of this function as a format string. For example for the "all()" function,
     * the signature would be this:
     *
     * "all(%s IN %s WHERE %s)"
     *
     * @return string
     */
    abstract protected function getSignature(): string;

    /**
     * The parameters for this function as QueryConvertable objects. These parameters are inserted, in order, into
     * the signature string retrieved from ::getSignature().
     *
     * @return AnyType[]
     */
    abstract protected function getParameters(): array;
}
