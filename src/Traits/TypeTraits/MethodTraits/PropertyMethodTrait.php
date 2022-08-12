<?php

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\MethodTraits;

use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Types\Methods\PropertyMethod;

/**
 * This trait provides a default implementation to satisfy the "PropertyFunction" interface.
 *
 * @implements PropertyMethod
 */
trait PropertyMethodTrait
{
    /**
     * @inheritDoc
     */
    public function property(string $property): Property
    {
        return new Property($this, $property);
    }
}
