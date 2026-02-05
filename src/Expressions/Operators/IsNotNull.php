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
 * Represents the IS NOT NULL comparison operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison Corresponding documentation on Neo4j.com
 */
final class IsNotNull extends ComparisonUnaryOperator
{
    /**
     * @inheritDoc
     */
    public function isPostfix(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "IS NOT NULL";
    }

    /**
     * @inheritDoc
     */
    protected function getPrecedence(): Precedence
    {
        return Precedence::FUNCTIONAL;
    }
}
