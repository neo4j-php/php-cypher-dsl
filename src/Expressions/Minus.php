<?php


namespace WikibaseSolutions\CypherDSL\Expressions;

/**
 * This class represents an application of the unary minus operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-using-the-unary-minus-operator
 */
class Minus implements Expression
{
	/**
	 * @var Expression The expression to negate
	 */
	private Expression $expression;

	/**
	 * Minus constructor.
	 *
	 * @param Expression $expression The expression to negate
	 */
	public function __construct(Expression $expression)
	{
		$this->expression = $expression;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		return sprintf("-%s", $this->expression->toQuery());
	}
}