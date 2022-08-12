<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

/**
 * Represents the IS NOT NULL comparison operator.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 49)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
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
}
