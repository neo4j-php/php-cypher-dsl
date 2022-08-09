<?php

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;

/**
 * This trait provides a default implementation to satisfy the "FloatType" interface.
 *
 * @implements FloatType
 */
trait FloatTypeTrait
{
    use NumeralTypeTrait;
}
