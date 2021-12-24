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

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Represents a property. A property in Cypher would be something like "n.prop" or "n.a".
 */
class Property implements BooleanType, NumeralType, StringType, MapType, PointType, ListType
{
    use EscapeTrait;
    use BooleanTypeTrait;
    use ListTypeTrait;
    use NumeralTypeTrait;
    use StringTypeTrait;
    use MapTypeTrait;

    /**
     * @var MapType The expression to which this property belongs
     */
    private MapType $expression;

    /**
     * @var string The name of the property
     */
    private string $property;

    /**
     * Property constructor.
     *
     * @param MapType $expression
     * @param string $property
     */
    public function __construct(MapType $expression, string $property)
    {
        $this->expression = $expression;
        $this->property = $property;
    }

    /**
     * Assign a value to this property.
     *
     * @param AnyType $value The value to assign
     * @return Assignment
     */
    public function assign(AnyType $value): Assignment
    {
        return new Assignment($this, $value);
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s.%s", $this->expression->toQuery(), $this->escape($this->property));
    }
}