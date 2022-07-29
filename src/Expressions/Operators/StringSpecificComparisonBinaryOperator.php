<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Represents a string-specific comparison binary operator. These are:
 *
 * - case-sensitive prefix search on strings: "STARTS WITH"
 * - case-sensitive suffix search on strings: "ENDS WITH"
 * - case-sensitive inclusion search in strings: "CONTAINS"
 * - regular expression: "~=" (not part of openCypher)
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical
 */
abstract class StringSpecificComparisonBinaryOperator extends ComparisonOperator
{
	use CastTrait;

	/**
	 * StringSpecificComparisonBinaryOperator constructor.
	 *
	 * @param StringType|string $left The left-hand of the comparison operator
	 * @param StringType|string $right The right-hand of the comparison operator
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct($left, $right, bool $insertParentheses = true)
	{
		parent::__construct(self::toStringType($left), self::toStringType($right), $insertParentheses);
	}
}