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
 * This class represents the application of a binary operator, such as "+", "/" and "*".
 */
abstract class BinaryOperator extends Operator
{
    /**
     * @var AnyType The left-hand of the expression
     */
    private AnyType $left;

    /**
     * @var AnyType The right-hand of the expression
     */
    private AnyType $right;

    /**
     * BinaryOperator constructor.
     *
     * @param AnyType $left The left-hand of the expression
     * @param AnyType $right The right-hand of the expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $left, AnyType $right, bool $insertParentheses = true)
    {
        parent::__construct($insertParentheses);

        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Gets the left-hand of the expression.
     *
     * @return AnyType
     */
    public function getLeft(): AnyType
    {
        return $this->left;
    }

    /**
     * Gets the right-hand of the expression.
     *
     * @return AnyType
     */
    public function getRight(): AnyType
    {
        return $this->right;
    }

    /**
     * @inheritDoc
     */
    protected function toInner(): string
    {
        return sprintf("%s %s %s", $this->left->toQuery(), $this->getOperator(), $this->right->toQuery());
    }

    /**
     * Returns the operator. For instance, this function would return "-" for the minus operator.
     *
     * @return string
     */
    abstract protected function getOperator(): string;
}
