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

use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;

/**
 * Represents the leaf type "boolean".
 *
 * @see BooleanTypeTrait for a default implementation
 */
interface BooleanType extends PropertyType
{
    /**
     * Create a conjunction between this expression and the given expression.
     */
    public function and(self|bool $right): Conjunction;

    /**
     * Create a disjunction between this expression and the given expression.
     */
    public function or(self|bool $right): Disjunction;

    /**
     * Perform an XOR with the given expression.
     */
    public function xor(self|bool $right): ExclusiveDisjunction;

    /**
     * Negate this expression (using the NOT operator).
     */
    public function not(): Negation;
}
