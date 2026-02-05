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
     * @param AnyType $left  The left-hand of the expression
     * @param AnyType $right The right-hand of the expression
     */
    public function __construct(AnyType $left, AnyType $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * Gets the left-hand of the expression.
     */
    public function getLeft(): AnyType
    {
        return $this->left;
    }

    /**
     * Gets the right-hand of the expression.
     */
    public function getRight(): AnyType
    {
        return $this->right;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $left = $this->shouldInsertParentheses($this->left) ?
            "({$this->left->toQuery()})" :
            $this->left->toQuery();

        $right = $this->shouldInsertParentheses($this->right) ?
            "({$this->right->toQuery()})" :
            $this->right->toQuery();

        return sprintf("%s %s %s", $left, $this->getOperator(), $right);
    }

    /**
     * Returns the operator. For instance, this function would return "-" for the minus operator.
     */
    abstract protected function getOperator(): string;
}
