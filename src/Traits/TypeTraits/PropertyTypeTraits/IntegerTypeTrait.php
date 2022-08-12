<?php

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * This trait provides a default implementation to satisfy the "IntegerType" interface.
 *
 * @implements IntegerType
 */
trait IntegerTypeTrait
{
    use NumeralTypeTrait;
}
