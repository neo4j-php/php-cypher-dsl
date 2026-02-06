<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Types\CompositeTypes;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Represent the leaf type "list".
 *
 * @see ListTypeTrait for a default implemenation
 */
interface ListType extends CompositeType
{
    /**
     * Checks whether the given element exists in this list.
     */
    public function has(PropertyType|string|int|float|bool $left): In;
}
