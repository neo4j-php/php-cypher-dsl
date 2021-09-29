<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

/**
 * Represents the application of the conjunction (AND) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean
 */
class AndOperator extends BinaryOperator
{
    protected function getOperator(): string
    {
        return "AND";
    }
}