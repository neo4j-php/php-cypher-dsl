<?php

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;

/**
 * Helper trait for casting native PHP types to Cypher-DSL types.
 */
trait CastTrait
{
	use ErrorTrait;

	/**
	 * Casts the given value to a ListType.
	 *
	 * @param ListType|array $list
	 * @return ListType
	 */
	private static function toListType($list): ListType
	{
		self::assertClass('list', [ListType::class, 'array'], $list);
		return $list instanceof ListType ? $list : new ExpressionList($list);
	}

	/**
	 * Casts the given value to a Variable.
	 *
	 * @param Variable|string $variable
	 * @return Variable
	 */
	private static function toVariable($variable): Variable
	{
		self::assertClass('variable', [Variable::class, 'string'], $variable);
		return $variable instanceof Variable ? $variable : new Variable($variable);
	}
}