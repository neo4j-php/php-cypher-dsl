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

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;

/**
 * This trait provides a default implementation to satisfy the "DateType" interface.
 *
 * @implements DateType
 */
trait DateTypeTrait
{
    use PropertyTypeTrait;
}
