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

use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents the application of a unary operator, such as "-" and "NOT".
 */
abstract class UnaryOperator extends Operator
{
    /**
     * @var AnyType The expression
     */
    private AnyType $expression;

    /**
     * @param AnyType $expression The unary expression
     */
    public function __construct(AnyType $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Returns whether this is a postfix operator or not.
     */
    public function isPostfix(): bool
    {
        return false;
    }

    /**
     * Returns the expression to negate.
     */
    public function getExpression(): AnyType
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $expression = $this->shouldInsertParentheses($this->expression) ?
            "({$this->expression->toQuery()})" :
            $this->expression->toQuery();

        return $this->isPostfix() ?
            sprintf("%s %s", $expression, $this->getOperator()) :
            sprintf("%s %s", $this->getOperator(), $expression);
    }

    /**
     * Returns the operator. For instance, this function would return "-" for the minus operator.
     */
    abstract protected function getOperator(): string;
}
