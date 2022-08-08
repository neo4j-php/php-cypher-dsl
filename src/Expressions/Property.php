<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalDateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\TimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
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
    NumeralType,
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
        ListTypeTrait,
        LocalDateTimeTypeTrait,
        LocalTimeTypeTrait,
        MapTypeTrait,
        NumeralTypeTrait,
        PointTypeTrait,
        StringTypeTrait,
        TimeTypeTrait;

    /**
     * @var MapType|NodeType|RelationshipType The expression to which this property belongs
     */
    private $expression;

    /**
     * @var Variable The name of the property
     */
    private Variable $property;

    /**
     * Property constructor.
     *
     * @param MapType|NodeType|RelationshipType $expression
     * @param Variable $property
     */
    public function __construct($expression, Variable $property)
    {
        self::assertClass('expression', [MapType::class, NodeType::class, RelationshipType::class], $expression);

        $this->expression = $expression;
        $this->property = $property;
    }

    /**
     * Assign a value to this property.
     *
     * @param AnyType $value The value to assign
     * @return PropertyReplacement
     */
    public function assign(AnyType $value): PropertyReplacement
    {
        return new PropertyReplacement($this, $value);
    }

    /**
     * Returns the property name.
     *
     * @return Variable
     */
    public function getProperty(): Variable
    {
        return $this->property;
    }

    /**
     * Returns the map type of the property.
     *
     * @return MapType|NodeType|RelationshipType
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s.%s", $this->expression->toQuery(), $this->property->toQuery());
    }
}
