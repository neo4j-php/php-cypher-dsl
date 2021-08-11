<?php


namespace WikibaseSolutions\CypherDSL\Expressions;

/**
 * This class represents the application of a binary operator, such as "+", "/" and "*".
 */
abstract class BinaryOperator implements Expression
{
	/**
	 * @var Expression The left-hand of the expression
	 */
	private Expression $left;

	/**
	 * @var Expression The right-hand of the expression
	 */
	private Expression $right;

	/**
	 * Plus constructor.
	 *
	 * @param Expression $left The left-hand of the expression
	 * @param Expression $right The right-hand of the expression
	 */
	public function __construct(Expression $left, Expression $right)
	{
		$this->left = $left;
		$this->right = $right;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		return sprintf("(%s %s %s)", $this->left->toQuery(), $this->getOperator(), $this->right->toQuery());
	}

	/**
	 * Returns the operator. For instance, this function would return "+" for the addition operator.
	 *
	 * @return string
	 */
	abstract protected function getOperator(): string;
}