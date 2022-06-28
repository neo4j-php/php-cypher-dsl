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

use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\NodeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\RelationshipTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\StringGenerationTrait;
use WikibaseSolutions\CypherDSL\Traits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalDateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\LocalTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TimeTypeTrait;
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
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\HasRelationshipsType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

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
    PointType,
    RelationshipType,
    StringType,
    TimeType
{
    use BooleanTypeTrait;
    use DateTypeTrait;
    use DateTimeTypeTrait;
    use ListTypeTrait;
    use LocalDateTimeTypeTrait;
    use LocalTimeTypeTrait;
    use MapTypeTrait;
    use NodeTypeTrait;
    use NumeralTypeTrait;
    use PointTypeTrait;
    use RelationshipTypeTrait;
    use StringTypeTrait;
    use TimeTypeTrait;

    use EscapeTrait;
    use StringGenerationTrait;
    use ErrorTrait;

    /**
     * @var string The name of this variable
     */
    private string $name;

    /**
     * Variable constructor.
     *
     * @param string|null $variable The variable
     */
    public function __construct(?string $variable = null)
    {
        if (!isset($variable)) {
            $variable = $this->generateString('var');
        }

        self::assertValidName($variable);

        $this->name = $variable;
    }

    /**
     * Adds the given labels to this variable.
     *
     * @param string[]|string $labels
     * @return Label
     */
    public function labeled($labels): Label
    {
        if (is_string($labels)) {
            $labels = [$labels];
        }

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

    /**
     * Returns the name of this variable.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function relationship(RelationshipType $relationship, HasRelationshipsType $nodeOrPath): Path
    {
        return $this->toNode()->relationship($relationship, $nodeOrPath);
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function relationshipTo(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        return $this->toNode()->relationshipTo($nodeOrPath, $type, $properties, $name);
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function relationshipFrom(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        return $this->toNode()->relationshipFrom($nodeOrPath, $type, $properties, $name);
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function relationshipUni(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        return $this->toNode()->relationshipUni($nodeOrPath, $type, $properties, $name);
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function withProperty(string $key, $value): Node
    {
        return $this->toNode()->withProperty($key, $value);
    }

    /**
     * @inheritdoc
     *
     * This function allows users to treat a variable as if it were a node.
     */
    public function withProperties($properties): Node
    {
        return $this->toNode()->withProperties($properties);
    }

    /**
     * @inheritDoc
     */
    public function getVariable(): Variable
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return self::escape($this->name);
    }

    /**
     * Returns a node with the name of this variable.
     *
     * @return Node
     */
    private function toNode(): Node
    {
        return Query::node()->named($this);
    }
}
