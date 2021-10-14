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

namespace WikibaseSolutions\CypherDSL\Expressions\Patterns;

use WikibaseSolutions\CypherDSL\EscapeTrait;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Types\NodeType;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * This class represents a node.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 */
class Node extends Pattern implements NodeType
{
    use EscapeTrait;

    /**
     * @var string[]
     */
    private array $labels = [];

    /**
     * @var Variable
     */
    private Variable $variable;

    /**
     * @var PropertyMap
     */
    private PropertyMap $properties;

    /**
     * Node constructor.
     *
     * @param string|null $label
     */
    public function __construct(string $label = null)
    {
        if ($label !== null) {
            $this->labels[] = $label;
        }
    }

    /**
     * @param  Variable|string $variable
     * @return Node
     */
    public function named($variable): self
    {
        if (!($variable instanceof Variable)) {
            $variable = new Variable($variable);
        }

        $this->variable = $variable;

        return $this;
    }

    /**
     * @param  PropertyMap|array $properties
     * @return Node
     */
    public function withProperties($properties): self
    {
        if (is_array($properties)) {
            $properties = new PropertyMap($properties);
        }

        $this->properties = $properties;

        return $this;
    }

    /**
     * @param  string $label
     * @return Node
     */
    public function withLabel(string $label): self
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        $nodeInner = "";

        if (isset($this->variable)) {
            $nodeInner .= $this->variable->toQuery();
        }

        if ($this->labels !== []) {
            foreach ($this->labels as $label) {
                $nodeInner .= ":{$this->escape($label)}";
            }
        }

        if (isset($this->properties)) {
            if ($nodeInner !== "") {
                $nodeInner .= " ";
            }

            $nodeInner .= $this->properties->toQuery();
        }

        return "($nodeInner)";
    }
}