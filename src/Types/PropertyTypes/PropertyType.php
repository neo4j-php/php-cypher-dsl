<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Types\PropertyTypes;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PropertyTypeTrait;
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
 * @note This interface should not be implemented by any class directly
 *
 * @see PropertyTypeTrait for a default implementation
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#property-types Corresponding documentation on Neo4j.com
 */
interface PropertyType extends AnyType
{
    /**
     * Checks whether the element exists in the given list.
     */
    public function in(ListType|array $right): In;
}
