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

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * This class represents any function call.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/
 */
abstract class FunctionCall extends Expression
{
    /**
     * Calls the "all()" function. The signature of the "all()" function is:
     *
     * all(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param  Expression $variable  A variable that can be used from within the predicate
     * @param  Expression $list      An expression that returns a list
     * @param  Expression $predicate A predicate that is tested against all items in the list
     * @return FunctionCall
     */
    public static function all(Expression $variable, Expression $list, Expression $predicate): FunctionCall
    {
        return new All($variable, $list, $predicate);
    }

    /**
     * Calls the "any()" function. The signature of the "any()" function is:
     *
     * any(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param  Expression $variable  A variable that can be used from within the predicate
     * @param  Expression $list      An expression that returns a list
     * @param  Expression $predicate A predicate that is tested against all items in the list
     * @return FunctionCall
     */
    public static function any(Expression $variable, Expression $list, Expression $predicate): FunctionCall
    {
        return new Any($variable, $list, $predicate);
    }

    /**
     * Calls the "exists()" function. The signature of the "exists()" function is:
     *
     * exists(input :: ANY?) :: (BOOLEAN?)
     *
     * @param  Expression $expression A pattern or property
     * @return FunctionCall
     */
    public static function exists(Expression $expression): FunctionCall
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
     * @param  Expression $list An expression that returns a list
     * @return FunctionCall
     */
    public static function isEmpty(Expression $list): FunctionCall
    {
        return new IsEmpty($list);
    }

    /**
     * Calls the "none()" function. The signature of the "none()" function is:
     *
     * none(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param  Expression $variable  A variable that can be used from within the predicate
     * @param  Expression $list      An expression that returns a list
     * @param  Expression $predicate A predicate that is tested against all items in the list
     * @return FunctionCall
     */
    public static function none(Expression $variable, Expression $list, Expression $predicate): FunctionCall
    {
        return new None($variable, $list, $predicate);
    }

    /**
     * Calls the "single()" function. The signature of the "single()" function is:
     *
     * single(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
     *
     * @param  Expression $variable  A variable that can be used from within the predicate
     * @param  Expression $list      An expression that returns a list
     * @param  Expression $predicate A predicate that is tested against all items in the list
     * @return FunctionCall
     */
    public static function single(Expression $variable, Expression $list, Expression $predicate): FunctionCall
    {
        return new Single($variable, $list, $predicate);
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
     * @return Expression[]
     */
    abstract protected function getParameters(): array;
}