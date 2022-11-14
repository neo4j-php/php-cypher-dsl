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
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-using-the-unary-minus-operator
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
}
