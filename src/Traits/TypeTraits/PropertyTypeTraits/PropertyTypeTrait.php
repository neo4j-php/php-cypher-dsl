<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This trait provides a default implementation to satisfy the "PropertyType" interface.
 *
 * This trait should not be used by any class directly. Instead, the following subtraits should be used where
 * appropriate:
 *
 * - BooleanTypeTrait
 * - DateTimeTypeTrait
 * - DateTypeTrait
 * - LocalDateTimeTypeTrait
 * - LocalTimeTypeTrait
 * - NumeralTypeTrait
 * - PointTypeTrait
 * - StringTypeTrait
 * - TimeTypeTrait
 */
trait PropertyTypeTrait
{
    use AnyTypeTrait;

    /**
     * @inheritDoc
     */
    public function in(ListType|array $right): In
    {
        return new In($this, CastUtils::toListType($right));
    }
}
