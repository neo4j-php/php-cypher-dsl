<?php

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

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
	 * Casts the given value to a StringType.
	 *
	 * @param StringType|string $string
	 * @return StringType
	 */
	private static function toStringType($string): StringType
	{
		self::assertClass('string', [StringType::class, 'string'], $string);
		return $string instanceof StringType ? $string : Literal::string($string);
	}

	/**
	 * Casts the given value to a NumeralType.
	 *
	 * @param NumeralType|int|float $numeral
	 * @return NumeralType
	 */
	private static function toNumeralType($numeral): NumeralType
	{
		self::assertClass('numeral', [NumeralType::class, 'int', 'float'], $numeral);
		return $numeral instanceof NumeralType ? $numeral : Literal::decimal($numeral);
	}

	/**
	 * Casts the given value to a BooleanType.
	 *
	 * @param BooleanType|bool $boolean
	 * @return BooleanType
	 */
	private static function toBooleanType($boolean): BooleanType
	{
		self::assertClass('boolean', [BooleanType::class, 'bool'], $boolean);
		return $boolean instanceof BooleanType ? $boolean : Literal::boolean($boolean);
	}

	/**
	 * Casts the given value to a PropertyType.
	 *
	 * @param PropertyType|bool|int|float|string $property
	 * @return PropertyType
	 */
	private static function toPropertyType($property): PropertyType
	{
		self::assertClass('property', [PropertyType::class, 'bool', 'int', 'float', 'string'], $property);
		return $property instanceof PropertyType ? $property : Literal::literal($property);
	}

	/**
	 * Casts the given value to an AnyType.
	 *
	 * @param AnyType|Pattern|int|float|string|bool|array $value
	 * @return AnyType
	 */
	private static function toAnyType($value): AnyType
	{
		self::assertClass('value', [AnyType::class, Pattern::class, 'int', 'float', 'string', 'bool', 'array'], $value);

		if ($value instanceof AnyType) {
			return $value;
		}

		if ($value instanceof Pattern) {
			return $value->getVariable();
		}

		if (is_array($value)) {
			return self::arrayIsList($value) ?
				new ExpressionList($value) :
				new PropertyMap($value);
		}

		return Literal::literal($value);
	}

	/**
	 * Polyfill for PHP8.1 "array_is_list" function.
	 *
	 * @author Nicolas Grekas <p@tchwork.com>
	 * @see https://github.com/symfony/polyfill-php81/blob/main/Php81.php
	 *
	 * @param array $array
	 * @return bool
	 */
	private static function arrayIsList(array $array): bool
	{
		if (version_compare(PHP_VERSION, "8.1") >= 0) {
			// If the version is at least PHP 8.1, use the native implementation
			return array_is_list($array);
		}

		if ([] === $array || $array === array_values($array)) {
			return true;
		}

		$nextKey = -1;

		foreach ($array as $k => $v) {
			if ($k !== ++$nextKey) {
				return false;
			}
		}

		return true;
	}
}