<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\StructuralTypeTraits;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;

/**
 * This trait provides a default implementation to satisfy the "StructuralType" interface.
 *
 * This trait should not be used by any class directly. Instead, the following sub-traits should be used where
 * appropriate:
 *
 * - NodeTypeTrait
 * - PathTypeTrait
 * - RelationshipTypeTrait
 */
trait StructuralTypeTrait
{
    use AnyTypeTrait;
}
