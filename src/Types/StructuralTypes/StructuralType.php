<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Types\StructuralTypes;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\StructuralTypeTraits\StructuralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents any structural type in Cypher.
 *
 * Structural types are types that:
 *
 * - can be returned from Cypher queries
 * - cannot be used as parameters
 * - cannot be stored as properties
 * - cannot be constructed with Cypher literals
 *
 * The structural types are:
 *
 * - node
 * - relationship
 * - path
 *
 * @note This interface should not be implemented by any class directly
 *
 * @see StructuralTypeTrait for a default implementation
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types Corresponding documentation on Neo4j.com
 */
interface StructuralType extends AnyType
{
}
