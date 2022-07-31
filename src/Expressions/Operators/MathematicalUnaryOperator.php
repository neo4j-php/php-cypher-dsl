<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a mathematical unary operator. These are:
 *
 * - unary minus: "-"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical
 */
abstract class MathematicalUnaryOperator extends UnaryOperator implements NumeralType
{
	use NumeralTypeTrait;

	/**
	 * MathematicalUnaryOperator constructor.
	 *
	 * @param NumeralType $expression The expression
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct(NumeralType $expression, bool $insertParentheses = true)
	{
		parent::__construct($expression, $insertParentheses);
	}
}