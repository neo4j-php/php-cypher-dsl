<?php


namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * Represents the "all()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-all
 */
class All extends FunctionCall
{
	/**
	 * @var QueryConvertable A variable that can be used from within the predicate
	 */
	private QueryConvertable $variable;

	/**
	 * @var QueryConvertable An expression that returns a list
	 */
	private QueryConvertable $list;

	/**
	 * @var QueryConvertable A predicate that is tested against all items in the list
	 */
	private QueryConvertable $predicate;

	/**
	 * All constructor. The signature of the "all()" function is:
	 *
	 * all(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
	 *
	 * @param QueryConvertable $variable A variable that can be used from within the predicate
	 * @param QueryConvertable $list An expression that returns a list
	 * @param QueryConvertable $predicate A predicate that is tested against all items in the list
	 */
	public function __construct(QueryConvertable $variable, QueryConvertable $list, QueryConvertable $predicate)
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