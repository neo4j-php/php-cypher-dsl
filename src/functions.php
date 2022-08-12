<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;

/**
 * @see Query::new()
 */
function query(): Query
{
    return Query::new();
}

/**
 * @see Query::new()
 */
function _(): Query
{
    return Query::new();
}

/**
 * @see Query::node()
 */
function node(string $label = null): Node
{
    return Query::node($label);
}

/**
 * @see Query::relationship()
 */
function relationship(array $direction): Relationship
{
    return Query::relationship($direction);
}

/**
 * @see Query::variable()
 */
function variable(?string $variable = null): Variable
{
    return Query::variable($variable);
}

/**
 * @see Query::literal()
 */
function literal($literal = null)
{
    return Query::literal($literal);
}

/**
 * @see Query::boolean()
 */
function boolean(bool $value): Boolean
{
    return Query::boolean($value);
}

/**
 * @see Query::string()
 */
function string(string $value): String_
{
    return Query::string($value);
}

/**
 * @see Query::integer()
 */
function integer(int $value): Integer
{
    return Query::integer($value);
}

/**
 * @see Query::float()
 */
function float(float $value): Float_
{
    return Query::float($value);
}

/**
 * @see Query::list()
 */
function list_(array $value): List_
{
    return Query::list($value);
}

/**
 * @see Query::map()
 */
function map(array $value): Map
{
    return Query::map($value);
}

/**
 * @see Query::function()
 */
function function_(): string
{
    return Query::function();
}

/**
 * @see Query::rawExpression()
 */
function raw(string $expression): RawExpression
{
    return Query::rawExpression($expression);
}
