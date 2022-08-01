<?php

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\StructuralTypeTraits;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This trait provides a default implementation to satisfy the "StructuralType" interface.
 *
 * This trait should not be used by any class directly. Instead, the following subtraits should be used where
 * appropriate:
 *
 * - NodeTypeTrait
 * - PathTypeTrait
 * - RelationshipTypeTrait
 *
 * @implements StructuralType
 */
trait StructuralTypeTrait
{
    use AnyTypeTrait;
}
