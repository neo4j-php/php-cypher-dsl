<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a comparison binary operator. These are:
 *
 * - equality: "="
 * - inequality: "<>"
 * - less than: "<"
 * - greater than: ">"
 * - less than or equal to: "<="
 * - greater than or equal to: ">="
 *
 * In additional, there are some string-specific comparison operators:
 *
 * - case-sensitive prefix search on strings: "STARTS WITH"
 * - case-sensitive suffix search on strings: "ENDS WITH"
 * - case-sensitive inclusion search in strings: "CONTAINS"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical
 */
abstract class ComparisonOperator extends Operator implements BooleanType
{
	use CastTrait;
	use BooleanTypeTrait;

	/**
	 * ComparisonBinaryOperator constructor.
	 *
	 * @param AnyType|string|int|float|bool|array $left The left-hand of the comparison operator
	 * @param AnyType|string|int|float|bool|array $right The right-hand of the comparison operator
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct($left, $right, bool $insertParentheses = true)
	{
		parent::__construct(self::toAnyType($left), self::toAnyType($right), $insertParentheses);
	}
}