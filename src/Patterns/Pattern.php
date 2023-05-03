<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;

/**
 * This class represents a pattern. A pattern can be:.
 *
 * - a node
 * - a path (alternating sequence of nodes and relationships)
 * - a relationship
 *
 * A pattern is not an expression, but rather a syntactic construct used for pattern matching on the graph
 * database. It therefore does not have any type, and thus it cannot be used in an expression. Instead, the variable
 * to which this pattern is assigned should be used. This library makes this easier by generating a variable for a
 * pattern when necessary and by casting Pattern instances to their associated variable when used in an expression.
 *
 * @note This interface should not be implemented by any class directly. Use any of the sub-interfaces instead:
 *
 * - CompletePattern: for patterns that can be matched in a MATCH clause
 * - PropertyPattern: for patterns that can contain properties
 * - RelatablePattern: for patterns that can be related to each other using a Relationship
 *
 * @see PatternTrait for a default implementation
 */
interface Pattern extends QueryConvertible
{
    /**
     * Returns whether a variable has been set for this pattern.
     */
    public function hasVariableSet(): bool;

    /**
     * Explicitly assign a named variable to this pattern.
     *
     * @param null|string|Variable $variable
     *
     * @return $this
     */
    public function withVariable($variable): self;

    /**
     * Returns the variable of this pattern. This function generates a variable if none has been set. It will implicitly set the variable of the pattern as well.
     */
    public function getVariable(): Variable;
}
