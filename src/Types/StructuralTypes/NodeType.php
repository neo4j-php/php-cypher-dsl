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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\StructuralTypeTraits\NodeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\Methods\PropertyMethod;

/**
 * Represents the leaf type "node".
 *
 * @see NodeTypeTrait for a default implementation
 */
interface NodeType extends PropertyMethod, StructuralType
{
}
