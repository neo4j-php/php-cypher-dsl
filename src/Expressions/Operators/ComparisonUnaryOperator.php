<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;

/**
 * Represents a comparison binary operator. These are:
 *
 * - "IS NULL"
 * - "IS NOT NULL"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
abstract class ComparisonUnaryOperator extends UnaryOperator implements BooleanType
{
	use BooleanTypeTrait;

	/**
	 * ComparisonUnaryOperator constructor.
	 *
	 * @param AnyType $expression The expression
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct(AnyType $expression, bool $insertParentheses = true)
	{
		parent::__construct($expression, $insertParentheses);
	}
}