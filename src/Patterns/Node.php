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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This class represents a node.
 *
 * @note This class does NOT implement NodeType, since it is not an expression. A node is a syntactic construct used for
 *  pattern matching, and does not represent the actual node itself. The variable in the node contains the actual
 *  value(s) of the matched node(s). However, because of the way the php-cypher-dsl is implemented, it often allows you
 *  to treat a Node object as if it were a NodeType object, automatically coalescing it to the variable that is
 *  contained within it.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 8)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 */
class Node extends Pattern
{
	use ErrorTrait;
    use EscapeTrait;

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
	 * Adds a new relationship from the end of the structural type to the node pattern.
	 *
	 * @param Relationship $relationship
	 * @param Node|Path $relatable
	 *
	 * @return Path
	 */
	public function relationship(Relationship $relationship, $relatable): Path
	{
		$this->assertClass('relatable', [Node::class, Path::class], $relatable);

		return (new Path($this))->relationship($relationship, $relatable);
	}

	/**
	 * Adds a new relationship to the node pattern at the end of the structural type to form a path.
	 *
	 * @param Node|Path $relatable The node to attach to the end of the structural type
	 * @param string|null $type The type of the relationship
	 * @param array|PropertyMap|null $properties The properties to attach to the relationship
	 * @param string|Variable|null $name The name fo the relationship
	 *
	 * @return Path
	 */
	public function relationshipTo($relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		$this->assertClass('relatable', [Node::class, Path::class], $relatable);

		return (new Path($this))->relationshipTo($relatable, $type, $properties, $name);
	}

	/**
	 * Adds a new relationship from the node pattern at the end of the structural type to form a path.
	 *
	 * @param Node|Path $relatable The node to attach to the end of the structural type.
	 * @param string|null $type The type of the relationship
	 * @param array|PropertyMap|null $properties The properties to attach to the relationship
	 * @param string|Variable|null $name The name fo the relationship
	 *
	 * @return Path
	 */
	public function relationshipFrom($relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		$this->assertClass('relatable', [Node::class, Path::class], $relatable);

		return (new Path($this))->relationshipFrom($relatable, $type, $properties, $name);
	}

	/**
	 * Adds a new unidirectional relationship to the node pattern at the end of the structural type to form a path.
	 *
	 * @param Node|Path $relatable The node to attach to the end of the structural type.
	 * @param string|null $type The type of the relationship
	 * @param array|PropertyMap|null $properties The properties to attach to the relationship
	 * @param string|Variable|null $name The name fo the relationship
	 *
	 * @return Path
	 */
	public function relationshipUni($relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		$this->assertClass('relatable', [Node::class, Path::class], $relatable);

		return (new Path($this))->relationshipUni($relatable, $type, $properties, $name);
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
