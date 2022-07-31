<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a mathematical binary operator. These are:
 *
 * - addition: "+"
 * - subtraction: "-"
 * - multiplication: "*"
 * - division: "/"
 * - modulo division: "%"
 * - exponentiation: "^"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical
 */
abstract class MathematicalBinaryOperator extends BinaryOperator implements NumeralType
{
	use NumeralTypeTrait;

	/**
	 * MathematicalBinaryOperator constructor.
	 *
	 * @param NumeralType $left The left-hand of the mathematical operator
	 * @param NumeralType $right The right-hand of the mathematical operator
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct(NumeralType $left, NumeralType $right, bool $insertParentheses = true)
	{
		parent::__construct($left, $right, $insertParentheses);
	}
}