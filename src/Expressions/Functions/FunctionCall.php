<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * This class represents any function call.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/
 */
abstract class FunctionCall implements Expression
{
	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		$signature = $this->getSignature();
		$parameters = array_map(
			fn (QueryConvertable $convertable): string => $convertable->toQuery(),
			$this->getParameters()
		);

		return sprintf($signature, $parameters);
	}

	/**
	 * Returns the signature of this function as a format string. For example for the "all()" function,
	 * the signature would be this:
	 *
	 * "all(%s IN $s WHERE %s)"
	 *
	 * @return string
	 */
	abstract public function getSignature(): string;

	/**
	 * The parameters for this function as QueryConvertable objects. These parameters are inserted, in order, into
	 * the signature string retrieved from ::getSignature().
	 *
	 * @return QueryConvertable[]
	 */
	abstract public function getParameters(): array;
}