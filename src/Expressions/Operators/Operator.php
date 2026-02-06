<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents the application of an operator, such as "NOT" or "*".
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/ Corresponding documentation on Neo4j.com
 */
abstract class Operator implements QueryConvertible
{
    /**
     * Returns the precedence. The precedence for Cypher operators (from low to high) is:
     *
     * 1. Disjunction (OR)
     * 2. Exclusive disjunction (XOR)
     * 3. Conjunction (AND)
     * 4. Negation (NOT)
     * 5. Comparison operators (=, <>, <, >, <=, >=)
     * 6. Additive operators (+, -)
     * 7. Multiplicative operators (*, /, %)
     * 8. Exponentiation (^)
     * 9. Unary operators (+, -)
     * 10. Functional operators (IN, STARTS WITH, ENDS WITH, CONTAINS, =~, IS NULL, IS NOT NULL)
     */
    abstract protected function getPrecedence(): Precedence;

    /**
     * Whether to insert parentheses around the given expression, given the precedence of this operator.
     */
    protected function shouldInsertParentheses(AnyType $expression): bool
    {
        if (!$expression instanceof self) {
            return false;
        }

        // When operators have equal precedence, the DSL inserts parentheses.
        // Handling associativity is possible, but would require extra logic, and
        // it’s unclear whether the added complexity would be worthwhile.
        return $expression->getPrecedence()->value <= $this->getPrecedence()->value;
    }
}
