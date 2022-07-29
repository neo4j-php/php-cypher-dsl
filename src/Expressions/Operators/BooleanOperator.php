<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a boolean binary operator. These are:
 *
 * - conjunction: "AND"
 * - disjunction: "OR"
 * - exclusive disjunction: "XOR"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 50)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean
 */
abstract class BooleanOperator extends Operator implements BooleanType
{
	use CastTrait;
	use BooleanTypeTrait;

	/**
	 * BooleanBinaryOperator constructor.
	 *
	 * @param BooleanType|bool $left The left-hand of the boolean operator
	 * @param BooleanType|bool $right The right-hand of the boolean operator
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct($left, $right, bool $insertParentheses = true)
	{
		parent::__construct(self::toBooleanType($left), self::toBooleanType($right), $insertParentheses);
	}
}