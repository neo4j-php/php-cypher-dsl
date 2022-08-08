<?php

namespace WikibaseSolutions\CypherDSL\Types\Methods;

use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTraits\PropertyMethodTrait;

/**
 * Represents the "property" method.
 *
 * @see PropertyMethodTrait for a default implementation
 */
interface PropertyMethod
{
    /**
     * Returns the property of the given name in this map.
     *
     * @param Variable|string $property
     * @return Property
     */
    public function property($property): Property;
}
