<?php


namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * Represents the "all()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-all
 */
class All extends FunctionCall
{
	/**
	 * @var Expression A variable that can be used from within the predicate
	 */
	private Expression $variable;

	/**
	 * @var Expression An expression that returns a list
	 */
	private Expression $list;

	/**
	 * @var Expression A predicate that is tested against all items in the list
	 */
	private Expression $predicate;

	/**
	 * All constructor. The signature of the "all()" function is:
	 *
	 * all(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
	 *
	 * @param Expression $variable A variable that can be used from within the predicate
	 * @param Expression $list An expression that returns a list
	 * @param Expression $predicate A predicate that is tested against all items in the list
	 */
	public function __construct(Expression $variable, Expression $list, Expression $predicate)
	{
		$this->variable = $variable;
		$this->list = $list;
		$this->predicate = $predicate;
	}

	/**
	 * @inheritDoc
	 */
	protected function getSignature(): string
	{
		return "all(%s IN %s WHERE %s)";
	}

	/**
	 * @inheritDoc
	 */
	protected function getParameters(): array
	{
		return [$this->variable, $this->list, $this->predicate];
	}
}