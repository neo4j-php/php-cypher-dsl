<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Types\Methods;

use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\MethodTraits\PropertyMethodTrait;

/**
 * Represents the "property" method.
 *
 * @see PropertyMethodTrait for a default implementation
 */
interface PropertyMethod
{
    /**
     * Returns the property of the given name in this map.
     */
    public function property(string $property): Property;
}
