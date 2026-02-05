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
 * Represents the application of the case-sensitive inclusion search (CONTAINS) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operator-comparison-string-specific Corresponding documentation on Neo4j.com
 */
final class Contains extends StringSpecificComparisonBinaryOperator
{
    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "CONTAINS";
    }

    /**
     * @inheritDoc
     */
    protected function getPrecedence(): Precedence
    {
        return Precedence::FUNCTIONAL;
    }
}
