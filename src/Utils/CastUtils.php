<?php

namespace WikibaseSolutions\CypherDSL\Utils;

use Stringable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * Utility functions for casting native PHP types to Cypher-DSL types. Casts are added to this class on an as-needed
 * basis.
 *
 * @internal This class is not covered by the backwards compatibility guarantee of php-cypher-dsl
 */
class CastUtils
{
    /**
     * Casts the given value to a ListType.
     */
    public static function toListType(ListType|array $list): ListType
    {
        return $list instanceof ListType ? $list : Literal::list($list);
    }

    /**
     * Casts the given value to a MapType.
     */
    public static function toMapType(MapType|array $map): MapType
    {
        return $map instanceof MapType ? $map : Literal::map($map);
    }

    /**
     * Casts the given value to a StringType.
     */
    public static function toStringType(StringType|string $string): StringType
    {
        return $string instanceof StringType ? $string : Literal::string($string);
    }

    /**
     * Casts the given value to a NumeralType.
     */
    public static function toNumeralType(NumeralType|float|int $numeral): NumeralType
    {
        return $numeral instanceof NumeralType ? $numeral : Literal::number($numeral);
    }

    /**
     * Casts the given value to an IntegerType.
     */
    public static function toIntegerType(IntegerType|int $integer): IntegerType
    {
        return $integer instanceof IntegerType ? $integer : Literal::integer($integer);
    }

    /**
     * Casts the given value to a BooleanType.
     */
    public static function toBooleanType(BooleanType|bool $boolean): BooleanType
    {
        return $boolean instanceof BooleanType ? $boolean : Literal::boolean($boolean);
    }

    /**
     * Casts the given value to a PropertyType.
     */
    public static function toPropertyType(PropertyType|bool|int|float|string $property): PropertyType
    {
        return $property instanceof PropertyType ? $property : Literal::literal($property);
    }

    /**
     * Casts the given value to a StructuralType.
     */
    public static function toStructuralType(StructuralType|Pattern $structure): StructuralType
    {
        return $structure instanceof StructuralType ? $structure : $structure->getVariable();
    }

    /**
     * Casts the given value to a Variable.
     *
     * @see CastTrait::toName() for a function that does not accept Pattern
     */
    public static function toVariable(Variable|Pattern|string $variable): Variable
    {
        if ($variable instanceof Variable) {
            return $variable;
        }

        if ($variable instanceof Pattern) {
            return $variable->getVariable();
        }

        return new Variable($variable);
    }

    /**
     * Casts the given value to a name.
     *
     * @see CastTrait::toVariable() for a function that accepts Pattern
     */
    public static function toName(Variable|string $name): Variable
    {
        return $name instanceof Variable ? $name : new Variable($name);
    }

    /**
     * Casts the given value to an AnyType.
     */
    public static function toAnyType(AnyType|Pattern|Stringable|bool|float|int|array|string $value): AnyType
    {
        if ($value instanceof Pattern) {
            return $value->getVariable();
        }

        if ($value instanceof AnyType) {
            return $value;
        }

        return Literal::literal($value);
    }
}