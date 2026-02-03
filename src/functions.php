<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;

/**
 * Creates a new Cypher query.
 *
 * @see Query::new()
 */
function query(): Query
{
    return Query::new();
}

/**
 * Creates a new Cypher query.
 *
 * @see Query::new()
 */
function _(): Query
{
    return Query::new();
}

/**
 * Creates a node.
 *
 * @param null|string $label The label to give to the node
 *
 * @see Query::node()
 */
function node(?string $label = null): Node
{
    return Query::node($label);
}

/**
 * Creates a relationship.
 *
 * @param string[] $direction The direction of the relationship (optional, default: unidirectional), should be either:
 *                            - Relationship::DIR_RIGHT (for a relation of (a)-->(b))
 *                            - Relationship::DIR_LEFT (for a relation of (a)<--(b))
 *                            - Relationship::DIR_UNI (for a relation of (a)--(b))
 *
 * @see Query::relationship()
 */
function relationship(array $direction = Relationship::DIR_UNI): Relationship
{
    return Query::relationship($direction);
}

/**
 * Creates a unidirectional relationship.
 *
 * @see Query::relationshipUni()
 */
function relationshipUni(): Relationship
{
    return Query::relationshipUni();
}

/**
 * Creates a right relationship.
 *
 * @see Query::relationshipTo()
 */
function relationshipTo(): Relationship
{
    return Query::relationshipTo();
}

/**
 * Creates a left relationship.
 *
 * @see Query::relationshipFrom()
 */
function relationshipFrom(): Relationship
{
    return Query::relationshipFrom();
}

/**
 * Creates a new variable with the given name, or generates a new variable with a random unique name.
 *
 * @param null|string $variable the name of the variable, or null to automatically generate a name
 *
 * @see Query::variable()
 */
function variable(?string $variable = null): Variable
{
    return Query::variable($variable);
}

/**
 * Creates a new literal from the given value. This function automatically constructs the appropriate
 * class based on the type of the value given.
 *
 * This function cannot be used directly to construct Point or Date types. Instead, you can create a Point literal
 * by using any of the following functions:
 *
 *  - literal()::point2d(...) - For a 2D cartesian point
 *  - literal()::point3d(...) - For a 3D cartesian point
 *  - literal()::point2dWGS84(...) - For a 2D WGS 84 point
 *  - literal()::point3dWGS84(...) - For a 3D WGS 84 point
 *
 * And a Date literal by using one of the following functions:
 *
 *  - literal()::date(...) - For the current date
 *  - literal()::dateYMD(...) - For a date from the given year, month and day
 *  - literal()::dateYWD(...) - For a date from the given year, week and day
 *  - literal()::dateString(...) - For a date from the given date string
 *  - literal()::dateTime(...) - For the current datetime
 *  - literal()::dateTimeYMD(...) - For a datetime from the given parameters (see function definition)
 *  - literal()::dateTimeYWD(...) - For a datetime from the given parameters (see function definition)
 *  - literal()::dateTimeYQD(...) - For a datetime from the given parameters (see function definition)
 *  - literal()::dateTimeYD(...) - For a datetime from the given parameters (see function definition)
 *  - literal()::dateTimeString(...) - For a datetime from the given datetime string
 *  - literal()::localDateTime(...) - For the current local datetime
 *  - literal()::localDateTimeYMD(...) - For a local datetime from the given parameters (see function definition)
 *  - literal()::localDateTimeYWD(...) - For a local datetime from the given parameters (see function definition)
 *  - literal()::localDateTimeYQD(...) - For a local datetime from the given parameters (see function definition)
 *  - literal()::localDateTimeYD(...) - For a local datetime from the given parameters (see function definition)
 *  - literal()::localDateTimeString(...) - For a local datetime from the given datetime string
 *  - literal()::localTimeCurrent(...) - For the current LocalTime
 *  - literal()::localTime(...) - For a local time from the given parameters (see function definition)
 *  - literal()::localTimeString(...) - For a local time from the given time string
 *  - literal()::time(...) - For the curren time
 *  - literal()::timeHMS(...) - For a time from the given hour, minute and second
 *  - literal()::timeString(...) - For a time from the given time string
 *
 * When no arguments are given to this function, the function will return a reference to the Literal class.
 *
 * You can directly call the constructors of the most basic types:
 *
 *  - boolean() - For a boolean
 *  - string() - For a string
 *  - integer() - For an integer
 *  - float() - For a float
 *  - list_() - For a list
 *  - map() - For a map
 *
 * @param null|bool|float|int|array|string $literal The literal to construct
 *
 * @see Query::literal()
 */
function literal(bool|float|int|array|string|null $literal = null): Boolean|Float_|List_|String_|Integer|string|Map
{
    return Query::literal($literal);
}

/**
 * Creates a new boolean.
 *
 * @see Query::boolean()
 */
function boolean(bool $value): Boolean
{
    return Query::boolean($value);
}

/**
 * Creates a new string.
 *
 * @see Query::string()
 */
function string(string $value): String_
{
    return Query::string($value);
}

/**
 * Creates a new integer.
 *
 * @see Query::integer()
 */
function integer(int $value): Integer
{
    return Query::integer($value);
}

/**
 * Creates a new float.
 *
 * @see Query::float()
 */
function float(float $value): Float_
{
    return Query::float($value);
}

/**
 * Creates a new list literal.
 *
 * @see Query::list()
 */
function list_(array $value): List_
{
    return Query::list($value);
}

/**
 * Creates a new map literal.
 *
 * @see Query::map()
 */
function map(array $value): Map
{
    return Query::map($value);
}

/**
 * Creates a new parameter.
 *
 * @param null|string $parameter The name of the parameter, or null to automatically generate a name
 *
 * @see Query::parameter()
 */
function parameter(?string $parameter = null): Parameter
{
    return Query::parameter($parameter);
}

/**
 * Returns the class string of the "Procedure" class.
 *
 * @return class-string<Procedure>
 *
 * @see Query::procedure()
 */
function function_(): string
{
    return Query::procedure();
}

/**
 * Returns the class string of the "Procedure" class.
 *
 * @return class-string<Procedure>
 *
 * @see Query::procedure()
 */
function procedure(): string
{
    return Query::procedure();
}

/**
 * Creates a new raw expression.
 *
 * @note This should be used only for features that are not implemented by the DSL.
 *
 * @param string $expression The raw expression
 *
 * @see Query::rawExpression()
 */
function raw(string $expression): RawExpression
{
    return Query::rawExpression($expression);
}
