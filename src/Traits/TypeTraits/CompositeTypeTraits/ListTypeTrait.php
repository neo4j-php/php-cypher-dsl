<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This trait provides a default implementation to satisfy the "ListType" interface.
 */
trait ListTypeTrait
{
    use CompositeTypeTrait;

    /**
     * @inheritDoc
     */
    public function has(PropertyType|string|int|float|bool $left): In
    {
        return new In(CastUtils::toPropertyType($left), $this);
    }
}
