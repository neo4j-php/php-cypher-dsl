<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Procedures;

use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\CompositeType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * This class represents any procedure.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/
 */
abstract class Procedure implements QueryConvertible
{
    use CastTrait;
    use ErrorTrait;

    /**
     * Produces a raw function call. This enables the usage of unimplemented functions in your
     * Cypher queries. The parameters of this function are not type-checked.
     *
     * @param string                                                                                                    $functionName The name of the function to call
     * @param AnyType|AnyType[]|bool|bool[]|float|float[]|int|int[]|mixed[]|mixed[][]|Pattern|Pattern[]|string|string[] $parameters   The parameters to pass to the function call
     */
    public static function raw(string $functionName, $parameters): Raw
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        $res = [];

        foreach ($parameters as $parameter) {
            $res[] = self::toAnyType($parameter);
        }

        return new Raw($functionName, $res);
    }

    /**
     * Calls the "all()" function. The signature of the "all()" function is:.
     *
     * all(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param string|Variable                               $variable  A variable that can be used from within the predicate
     * @param ListType|mixed[]                              $list      A list
     * @param AnyType|bool|float|int|mixed[]|Pattern|string $predicate A predicate that is tested against all items in the list
     */
    public static function all($variable, $list, $predicate): All
    {
        return new All(self::toName($variable), self::toListType($list), self::toAnyType($predicate));
    }

    /**
     * Calls the "any()" function. The signature of the "any()" function is:.
     *
     * any(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param string|Variable                               $variable  A variable that can be used from within the predicate
     * @param ListType|mixed[]                              $list      A list
     * @param AnyType|bool|float|int|mixed[]|Pattern|string $predicate A predicate that is tested against all items in the list
     */
    public static function any($variable, $list, $predicate): Any
    {
        return new Any(self::toName($variable), self::toListType($list), self::toAnyType($predicate));
    }

    /**
     * Calls the "exists()" function. The signature of the "exists()" function is:.
     *
     * exists(input :: ANY?) :: (BOOLEAN?)
     *
     * @param AnyType|bool|float|int|mixed[]|Pattern|string $expression A pattern or property
     */
    public static function exists($expression): Exists
    {
        return new Exists(self::toAnyType($expression));
    }

    /**
     * Calls the "isEmpty()" function. The signature of the "isEmpty()" function is:.
     *
     * isEmpty(input :: LIST? OF ANY?) :: (BOOLEAN?) - to check whether a list is empty
     * isEmpty(input :: MAP?) :: (BOOLEAN?) - to check whether a map is empty
     * isEmpty(input :: STRING?) :: (BOOLEAN?) - to check whether a string is empty
     *
     * @param ListType|MapType|StringType $list An expression that returns a list
     */
    public static function isEmpty($list): IsEmpty
    {
        self::assertClass('list', [CompositeType::class, StringType::class, 'string', 'array'], $list);

        if (!$list instanceof AnyType) {
            $list = Literal::literal($list);
        }

        // @phpstan-ignore-next-line
        return new IsEmpty($list);
    }

    /**
     * Calls the "none()" function. The signature of the "none()" function is:.
     *
     * none(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param string|Variable                               $variable  A variable that can be used from within the predicate
     * @param ListType|mixed[]                              $list      A list
     * @param AnyType|bool|float|int|mixed[]|Pattern|string $predicate A predicate that is tested against all items in the list
     */
    public static function none($variable, $list, $predicate): None
    {
        return new None(self::toName($variable), self::toListType($list), self::toAnyType($predicate));
    }

    /**
     * Calls the "single()" function. The signature of the "single()" function is:.
     *
     * single(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param string|Variable                               $variable  A variable that can be used from within the predicate
     * @param ListType|mixed[]                              $list      A list
     * @param AnyType|bool|float|int|mixed[]|Pattern|string $predicate A predicate that is tested against all items in the list
     */
    public static function single($variable, $list, $predicate): Single
    {
        return new Single(self::toName($variable), self::toListType($list), self::toAnyType($predicate));
    }

    /**
     * Calls the "point()" function. The signature of the "point()" function is:.
     *
     * point(input :: MAP?) :: (POINT?)
     *
     * @param MapType|mixed[] $map The map to use for constructing the point
     *
     * @see Literal::point2d()
     * @see Literal::point2dWGS84()
     * @see Literal::point3d()
     * @see Literal::point3dWGS84()
     */
    public static function point($map): Point
    {
        return new Point(self::toMapType($map));
    }

    /**
     * Calls the "date()" function. The signature of the "date()" function is:.
     *
     * date(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATE?)
     *
     * @param null|AnyType|bool|float|int|mixed[]|Pattern|string $value The input to the date function, from which to construct the date
     *
     * @see Literal::date()
     * @see Literal::dateString()
     * @see Literal::dateYWD()
     * @see Literal::dateYMD()
     */
    public static function date($value = null): Date
    {
        return new Date($value === null ? $value : self::toAnyType($value));
    }

    /**
     * Calls the "datetime()" function. The signature of the "datetime()" function is:.
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATETIME?)
     *
     * @param null|AnyType|bool|float|int|mixed[]|Pattern|string $value The input to the datetime function, from which to construct the datetime
     *
     * @see Literal::dateTime()
     * @see Literal::dateTimeString()
     * @see Literal::dateTimeYD()
     * @see Literal::dateTimeYWD()
     * @see Literal::dateTimeYMD()
     * @see Literal::dateTimeYQD()
     */
    public static function datetime($value = null): DateTime
    {
        return new DateTime($value === null ? $value : self::toAnyType($value));
    }

    /**
     * Calls the "localdatetime()" function. The signature of the "localdatetime()" function is:.
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (LOCALDATETIME?)
     *
     * @param null|AnyType|bool|float|int|mixed[]|Pattern|string $value The input to the localdatetime function, from which to construct the localdatetime
     *
     * @see Literal::localDateTime()
     * @see Literal::localDateTimeString()
     * @see Literal::localDateTimeYD()
     * @see Literal::localDateTimeYWD()
     * @see Literal::localDateTimeYMD()
     * @see Literal::localDateTimeYQD()
     */
    public static function localdatetime($value = null): LocalDateTime
    {
        return new LocalDateTime($value === null ? $value : self::toAnyType($value));
    }

    /**
     * Calls the "localtime()" function. The signature of the "localtime()" function is:.
     *
     * localtime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (LOCALTIME?)
     *
     * @param null|AnyType|bool|float|int|mixed[]|Pattern|string $value The input to the localtime function, from which to construct the localtime
     *
     * @see Literal::localTime()
     * @see Literal::localTimeCurrent()
     * @see Literal::localTimeString()
     */
    public static function localtime($value = null): LocalTime
    {
        return new LocalTime($value === null ? $value : self::toAnyType($value));
    }

    /**
     * Calls the "time()" function. The signature of the "time()" function is:.
     *
     * time(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (TIME?)
     *
     * @param null|AnyType|bool|float|int|mixed[]|Pattern|string $value The input to the localtime function, from which to construct the time
     *
     * @see Literal::time()
     * @see Literal::timeHMS()
     * @see Literal::timeString()
     */
    public static function time($value = null): Time
    {
        return new Time($value === null ? $value : self::toAnyType($value));
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $signature = $this->getSignature();
        $parameters = array_map(
            static fn (AnyType $value): string => $value->toQuery(),
            $this->getParameters()
        );

        return sprintf($signature, ...$parameters);
    }

    /**
     * Returns the signature of this function as a format string. For example for the "all()" function,
     * the signature would be this:.
     *
     * "all(%s IN %s WHERE %s)"
     */
    abstract protected function getSignature(): string;

    /**
     * The parameters for this function as QueryConvertable objects. These parameters are inserted, in order, into
     * the signature string retrieved from $this->getSignature().
     *
     * @return AnyType[]
     */
    abstract protected function getParameters(): array;
}
