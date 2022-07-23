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
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
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
	 * Casts the given value to a MapType.
	 *
	 * @param MapType|array $map
	 * @return MapType
	 */
	private static function toMapType($map): MapType
	{
		self::assertClass('map', [MapType::class, 'array'], $map);
		return $map instanceof MapType ? $map : new PropertyMap($map);
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
	 * Casts the given value to a NodeType.
	 *
	 * @param NodeType|Node $node
	 * @return NodeType
	 */
	private static function toNodeType($node): NodeType
	{
		self::assertClass('node', [NodeType::class, Node::class], $node);
		return $node instanceof NodeType ? $node : $node->getVariable();
	}

	/**
	 * Casts the given value to a PathType.
	 *
	 * @param PathType|Path $path
	 * @return PathType
	 */
	private static function toPathType($path): PathType
	{
		self::assertClass('path', [PathType::class, Path::class], $path);
		return $path instanceof PathType ? $path : $path->getVariable();
	}

	/**
	 * Casts the given value to a RelationshipType.
	 *
	 * @param RelationshipType|Relationship $relationship
	 * @return RelationshipType
	 */
	private static function toRelationshipType($relationship): RelationshipType
	{
		self::assertClass('relationship', [RelationshipType::class, Relationship::class], $relationship);
		return $relationship instanceof RelationshipType ? $relationship : $relationship->getVariable();
	}

	/**
	 * Casts the given value to a StructuralType.
	 *
	 * @param StructuralType|Pattern $pattern
	 * @return StructuralType
	 */
	private static function toStructuralType($pattern): StructuralType
	{
		self::assertClass('pattern', [StructuralType::class, Pattern::class], $pattern);
		return $pattern instanceof StructuralType ? $pattern : $pattern->getVariable();
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