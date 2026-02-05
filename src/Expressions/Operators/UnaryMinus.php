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
 * This class represents an application of the unary minus (-) operator.
 *
 * @see Subtraction for the binary subtraction (-) operator
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-using-the-unary-minus-operator Corresponding documentation on Neo4j.com
 */
final class UnaryMinus extends MathematicalUnaryOperator
{
    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "-";
    }

    /**
     * @inheritDoc
     */
    protected function getPrecedence(): Precedence
    {
        return Precedence::UNARY;
    }
}
