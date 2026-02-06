<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Types\PropertyTypes;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;

/**
 * Represents the leaf type "string".
 *
 * @see StringTypeTrait for a default implementation
 */
interface StringType extends PropertyType
{
    /**
     * Check whether this expression the given expression.
     */
    public function contains(self|string $right): Contains;

    /**
     * Perform a suffix string search with the given expression.
     */
    public function endsWith(self|string $right): EndsWith;

    /**
     * Perform a prefix string search with the given expression.
     */
    public function startsWith(self|string $right): StartsWith;

    /**
     * Perform a regex comparison with the given expression.
     */
    public function regex(self|string $right): Regex;
}
