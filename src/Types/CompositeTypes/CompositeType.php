<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Types\CompositeTypes;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\CompositeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents any composite type in Cypher.
 *
 * Composite types are types that:
 *
 * - can be returned from Cypher queries
 * - can be used as parameters
 * - cannot be stored as properties
 * - can be constructed with Cypher literals
 *
 * The composite types are:
 *
 * - list
 * - map
 *
 * @note This interface should not be implemented by any class directly
 *
 * @see CompositeTypeTrait for a default implemenation
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#composite-types Corresponding documentation on Neo4j.com
 */
interface CompositeType extends AnyType
{
}
