<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Types;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;

/**
 * Represents any type in Cypher.
 *
 * @note This interface should not be implemented by any class directly
 *
 * @see AnyTypeTrait for a default implementation
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/ Corresponding documentation on Neo4j.com
 */
interface AnyType extends QueryConvertible
{
    /**
     * Creates an alias of the current expression.
     */
    public function alias(Variable|string $right): Alias;

    /**
     * Perform an equality check with the given expression.
     */
    public function equals(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): Equality;

    /**
     * Perform an inequality comparison against the given expression.
     */
    public function notEquals(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): Inequality;

    /**
     * Perform a greater than comparison against the given expression.
     */
    public function gt(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): GreaterThan;

    /**
     * Perform a greater than or equal comparison against the given expression.
     */
    public function gte(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): GreaterThanOrEqual;

    /**
     * Perform a less than comparison against the given expression.
     */
    public function lt(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): LessThan;

    /**
     * Perform a less than or equal comparison against the given expression.
     */
    public function lte(self|Pattern|string|bool|float|int|array $right, bool $insertParentheses = true): LessThanOrEqual;

    /**
     * Checks whether the element is null.
     */
    public function isNull(bool $insertParentheses = true): IsNull;

    /**
     * Checks whether the element is not null.
     */
    public function isNotNull(bool $insertParentheses = true): IsNotNull;
}
