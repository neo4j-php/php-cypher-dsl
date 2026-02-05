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

/**
 * Represents the application of the conjunction (AND) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean Corresponding documentation on Neo4j.com
 */
final class Conjunction extends BooleanBinaryOperator
{
    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "AND";
    }

    /**
     * @inheritDoc
     */
    protected function getPrecedence(): Precedence
    {
        return Precedence::CONJUNCTION;
    }
}
