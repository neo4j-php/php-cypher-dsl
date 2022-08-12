<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\FloatTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\IntegerTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalDateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\TimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * Represents a property. A property in Cypher would be something like "n.prop" or "n.a".
 */
final class Property implements
    BooleanType,
    DateType,
    DateTimeType,
    FloatType,
    IntegerType,
    StringType,
    MapType,
    PointType,
    ListType,
    LocalDateTimeType,
    LocalTimeType,
    TimeType
{
    use BooleanTypeTrait,
        DateTypeTrait,
        DateTimeTypeTrait,
        EscapeTrait,
        FloatTypeTrait,
        IntegerTypeTrait,
        ListTypeTrait,
        LocalDateTimeTypeTrait,
        LocalTimeTypeTrait,
        MapTypeTrait,
        PointTypeTrait,
        StringTypeTrait,
        TimeTypeTrait;

    use CastTrait;

    /**
     * @var MapType|NodeType|RelationshipType The expression to which this property belongs
     */
    private AnyType $expression;

    /**
     * @var string The name of the property
     */
    private string $property;

    /**
     * Property constructor.
     *
     * @param MapType|NodeType|RelationshipType $expression  The expression that has the property
     * @param string                            $property    The name of the property
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct($expression, string $property)
    {
        self::assertClass('expression', [MapType::class, NodeType::class, RelationshipType::class], $expression);

        $this->expression = $expression;
        $this->property = $property;
    }

    /**
     * Replace the value of this property with something else.
     *
     * TODO: Disallow this function to be used outside the context of a SET
     *
     * @note This function only makes sense when used in the context of a SET
     * @param PropertyType|bool|int|float|string $value The new value to give to this property
     * @return PropertyReplacement
     *
     * TODO: Allow this function to take arrays of property types
     */
    public function replaceWith(AnyType $value): PropertyReplacement
    {
        return new PropertyReplacement($this, self::toPropertyType($value));
    }

    /**
     * Returns the property name.
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * Returns the expression to which the property belongs.
     *
     * @return MapType|NodeType|RelationshipType
     */
    public function getExpression(): AnyType
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s.%s", $this->expression->toQuery(), $this->escape($this->property));
    }
}
