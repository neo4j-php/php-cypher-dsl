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
     * UnaryOperator constructor.
     *
     * @param AnyType $expression The expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        parent::__construct($insertParentheses);

        $this->expression = $expression;
    }

    /**
     * Returns whether this is a postfix operator or not.
     *
     * @return bool
     */
    public function isPostfix(): bool
    {
        return false;
    }

    /**
     * Returns the expression to negate.
     *
     * @return AnyType
     */
    public function getExpression(): AnyType
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    protected function toInner(): string
    {
        $expression = $this->expression->toQuery();
        $operator = $this->getOperator();

        return $this->isPostfix() ?
            sprintf("%s %s", $expression, $operator) :
            sprintf("%s %s", $operator, $expression);
    }

    /**
     * Returns the operator. For instance, this function would return "-" for the minus operator.
     *
     * @return string
     */
    abstract protected function getOperator(): string;
}
