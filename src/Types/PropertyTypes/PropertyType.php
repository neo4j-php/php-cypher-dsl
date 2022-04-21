<?php

namespace WikibaseSolutions\CypherDSL\Types\PropertyTypes;

use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\In;
use WikibaseSolutions\CypherDSL\Inequality;
use WikibaseSolutions\CypherDSL\IsNotNull;
use WikibaseSolutions\CypherDSL\IsNull;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;

/**
 * Represents any property type in Cypher.
 *
 * Property types are types that:
 *
 * - can be returned from Cypher queries
 * - can be used as parameters
 * - can be stored as properties
 * - can be constructed with Cypher literals
 *
 * The property types are:
 *
 * - number, which has subtypes integer and float
 * - string
 * - boolean
 * - point
 * - temporal, which has subtypes date, time, localtime, datetime, localdatetime and duration
 *
 * Homogeneous lists of simple types can also be stored as properties, although lists in
 * general cannot.
 *
 * @note This interface should not be implemented by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#property-types
 */
interface PropertyType extends AnyType
{
    /**
     * Perform an equality check with the given expression.
     *
     * @param PropertyType $right
     * @return Equality
     */
    public function equals(PropertyType $right): Equality;

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param PropertyType $right
     * @return Inequality
     */
    public function notEquals(PropertyType $right): Inequality;

    /**
     * Checks whether the element exists in the given list.
     *
     * @param ListType $right
     * @return In
     */
    public function in(ListType $right): In;

    /**
     * Checks whether the element is null.
     *
     * @param bool $insertsParentheses whether to insert parentheses.
     *
     * @return IsNull
     */
    public function isNull(bool $insertsParentheses = true): IsNull;

    /**
     * Checks whether the element is not null.
     *
     * @param bool $insertsParentheses whether to insert parentheses.
     *
     * @return IsNotNull
     */
    public function isNotNull(bool $insertsParentheses = true): IsNotNull;
}
