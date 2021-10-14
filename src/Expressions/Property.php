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

use WikibaseSolutions\CypherDSL\EscapeTrait;
use WikibaseSolutions\CypherDSL\Expressions\Types\BooleanType;
use WikibaseSolutions\CypherDSL\Expressions\Types\ListType;
use WikibaseSolutions\CypherDSL\Expressions\Types\MapType;
use WikibaseSolutions\CypherDSL\Expressions\Types\NodeType;
use WikibaseSolutions\CypherDSL\Expressions\Types\NumeralType;
use WikibaseSolutions\CypherDSL\Expressions\Types\StringType;

/**
 * Represents a property. A property in Cypher would be something like "n.prop" or "n.a".
 */
class Property extends Expression implements BooleanType, NumeralType, StringType, MapType, ListType
{
    use EscapeTrait;

    /**
     * @var NodeType The expression to which this property belongs
     */
    private NodeType $expression;

    /**
     * @var string The name of the property
     */
    private string $property;

    /**
     * Property constructor.
     *
     * @param NodeType $expression
     * @param string     $property
     */
    public function __construct(NodeType $expression, string $property)
    {
        $this->expression = $expression;
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s.%s", $this->expression->toQuery(), $this->escape($this->property));
    }
}