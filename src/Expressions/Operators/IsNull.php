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
 * Represents the IS NULL comparison operator.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 49)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
final class IsNull extends ComparisonUnaryOperator
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
        return "IS NULL";
    }
}
