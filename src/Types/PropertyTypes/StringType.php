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
     *
     * @param string|StringType $right
     */
    public function contains($right, bool $insertParentheses = true): Contains;

    /**
     * Perform a suffix string search with the given expression.
     *
     * @param string|StringType $right
     */
    public function endsWith($right, bool $insertParentheses = true): EndsWith;

    /**
     * Perform a prefix string search with the given expression.
     *
     * @param string|StringType $right
     */
    public function startsWith($right, bool $insertParentheses = true): StartsWith;

    /**
     * Perform a regex comparison with the given expression.
     *
     * @param string|StringType $right
     */
    public function regex($right, bool $insertParentheses = true): Regex;
}
