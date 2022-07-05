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

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\PropertiesTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\VariableTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\NodeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;

/**
 * This class represents a node.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 8)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 */
class Node implements NodeType
{
    use NodeTypeTrait;

    use EscapeTrait;
	use VariableTrait;

    /**
     * @var string[] The labels of this node
     */
    private array $labels = [];

    /**
     * @var MapType|null The properties of this relationship
     */
    private ?MapType $properties = null;

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
     * Sets the labels of this node. This overwrites any previously set labels.
     *
     * @param string[] $labels
     * @return $this
     */
    public function withLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Adds a label to the node.
     *
     * @param string $label
     * @return $this
     */
    public function addLabel(string $label): self
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * Set the properties of this node.
     *
     * @param MapType $properties
     * @return $this
     */
    public function withProperties(MapType $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

	/**
	 * Returns the labels of the node.
	 *
	 * @return string[]
	 */
	public function getLabels(): array
	{
		return $this->labels;
	}

    /**
     * Returns the properties of this node.
     *
     * @return MapType
     */
    public function getProperties(): ?MapType
    {
        return $this->properties;
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
