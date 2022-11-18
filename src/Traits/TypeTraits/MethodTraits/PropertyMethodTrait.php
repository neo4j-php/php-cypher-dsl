<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\MethodTraits;

use WikibaseSolutions\CypherDSL\Expressions\Property;

/**
 * This trait provides a default implementation to satisfy the "PropertyFunction" interface.
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
