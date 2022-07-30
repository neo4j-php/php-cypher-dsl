<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a boolean unary operator. These are:
 *
 * - negation: "NOT"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 50)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean
 */
abstract class BooleanUnaryOperator extends UnaryOperator implements BooleanType
{
	use CastTrait;
	use BooleanTypeTrait;

	/**
	 * BooleanUnaryOperator constructor.
	 *
	 * @param BooleanType|bool $expression The expression
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct($expression, bool $insertParentheses = true)
	{
		parent::__construct(self::toBooleanType($expression), $insertParentheses);
	}
}