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
use WikibaseSolutions\CypherDSL\Traits\DateTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\DateTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\HasNameTrait;
use WikibaseSolutions\CypherDSL\Traits\HasPropertiesTrait;
use WikibaseSolutions\CypherDSL\Traits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalDateTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalTimeTrait;
use WikibaseSolutions\CypherDSL\Traits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\PropertyTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\RelationshipTrait;
use WikibaseSolutions\CypherDSL\Traits\PointTrait;
use WikibaseSolutions\CypherDSL\Traits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\StructuralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TimeTrait;
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
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;

/**
 * Represents a variable.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/variables/
 */
class Variable implements
    BooleanType,
    DateType,
    DateTimeType,
    ListType,
    LocalDateTimeType,
    LocalTimeType,
    MapType,
    NodeType,
    NumeralType,
    PathType,
    PointType,
    StringType,
    TimeType
{
    use EscapeTrait;
    use DateTrait;
    use DateTimeTrait;
    use BooleanTypeTrait;
    use ListTypeTrait;
    use LocalDateTimeTrait;
    use LocalTimeTrait;
    use NumeralTypeTrait;
    use StructuralTypeTrait;
    use PointTrait;
    use StringTypeTrait;
    use TimeTrait;
    use HasNameTrait;
    use MapTypeTrait;

    /**
     * @var string The variable
     */
    private string $variable;

    /**
     * Variable constructor.
     *
     * @param string|null $variable The variable
     */
    public function __construct(?string $variable = null)
    {
        $variable ??= self::generateName('var');

        $variable = trim($variable);

        self::validateName($variable);

        $this->variable = $variable;
    }

    /**
     * Adds the given labels to this variable.
     *
     * @param string[] $labels
     * @return Label
     * @deprecated Use Variable::labeled() instead
     */
    public function withLabels(array $labels): Label
    {
        return $this->labeled($labels);
    }

    /**
     * Adds the given labels to this variable.
     *
     * @param array $labels
     * @return Label
     */
    public function labeled(array $labels): Label
    {
        return new Label($this, $labels);
    }

    /**
     * Assign a value to this variable.
     *
     * @param AnyType $value The value to assign
     * @return Assignment
     */
    public function assign(AnyType $value): Assignment
    {
        return new Assignment($this, $value);
    }

    public function getName(): string
    {
        return $this->variable;
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return $this->escape($this->variable);
    }
}
