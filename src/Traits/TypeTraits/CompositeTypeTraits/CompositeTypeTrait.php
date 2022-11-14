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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\CompositeType;

/**
 * This trait provides a default implementation to satisfy the "CompositeType" interface.
 *
 * This trait should not be used by any class directly. Instead, the following subtraits should be used where
 * appropriate:
 *
 * - ListTypeTrait
 * - MapTypeTrait
 *
 * @implements CompositeType
 */
trait CompositeTypeTrait
{
    use AnyTypeTrait;
}
