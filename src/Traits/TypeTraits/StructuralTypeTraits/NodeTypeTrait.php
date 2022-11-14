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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\MethodTraits\PropertyMethodTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;

/**
 * This trait provides a default implementation to satisfy the "NodeType" interface.
 *
 * @implements NodeType
 */
trait NodeTypeTrait
{
    use PropertyMethodTrait;
    use StructuralTypeTrait;
}
