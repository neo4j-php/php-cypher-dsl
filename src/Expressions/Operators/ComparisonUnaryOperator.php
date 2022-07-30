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
 * - "IS NULL"
 * - "IS NOT NULL"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
abstract class ComparisonUnaryOperator extends UnaryOperator implements BooleanType
{
	use CastTrait;
	use BooleanTypeTrait;

	/**
	 * ComparisonUnaryOperator constructor.
	 *
	 * @param AnyType|string|int|float|bool|array $expression The expression
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
	 */
	public function __construct($expression, bool $insertParentheses = true)
	{
		parent::__construct(self::toAnyType($expression), $insertParentheses);
	}
}