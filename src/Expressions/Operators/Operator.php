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

use WikibaseSolutions\CypherDSL\QueryConvertible;

/**
 * This class represents the application of an operator, such as "NOT" or "*".
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 46)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/
 */
abstract class Operator implements QueryConvertible
{
    /**
     * @var bool Whether to insert parentheses around the expression
     */
    private bool $insertParentheses;

    /**
     * UnaryOperator constructor.
     *
     * @param bool $insertParentheses Whether to insert parentheses around the application of the operator
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(bool $insertParentheses = true)
    {
        $this->insertParentheses = $insertParentheses;
    }

    /**
     * Returns whether the operator inserts parenthesis.
     *
     * @return bool
     */
    public function insertsParentheses(): bool
    {
        return $this->insertParentheses;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $format = $this->insertParentheses ? "(%s)" : "%s";
        $inner = $this->toInner();

        return sprintf($format, $inner);
    }

    /**
     * Returns the inner part of the application of the operator, that is, without any parentheses.
     *
     * @return string
     */
    abstract protected function toInner(): string;
}
