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

use WikibaseSolutions\CypherDSL\Encoder;

/**
 * This class represents a node.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 * @package WikibaseSolutions\CypherDSL
 */
class Node implements Pattern
{
	use Encoder;

	/**
	 * @var string
	 */
	private string $label = "";

	/**
	 * @var string
	 */
	private string $variable = "";

	/**
	 * @var array
	 */
	private array $properties = [];

	/**
	 * Node constructor.
	 * @param string|null $label
	 */
	public function __construct(string $label = "")
	{
		$this->label = $label;
	}

	/**
	 * @param string $variable
	 * @return Node
	 */
	public function named(string $variable): self
	{
		$this->variable = $variable;

		return $this;
	}

	/**
	 * @param string $label
	 * @return Node
	 */
	public function withLabel(string $label): self
	{
		$this->label = $label;

		return $this;
	}

	/**
	 * @param array $properties
	 * @return Node
	 */
	public function withProperties(array $properties): self
	{
		$this->properties = $properties;

		return $this;
	}

	/**
	 * Returns the string representation of this relationship that can be used directly
	 * in a query.
	 *
	 * @return string
	 */
	public function toString(): string
	{
		$nodeInner = "";

		if ($this->variable !== "") {
			$nodeInner .= $this->escapeName($this->variable);
		}

		if ($this->label !== "") {
			$nodeInner .= ":{$this->escapeName($this->label)}";
		}

		if (count($this->properties) !== 0) {
			if ($nodeInner !== "") $nodeInner .= " ";
			$nodeInner .= $this->encodePropertyList($this->properties);
		}

		return "($nodeInner)";
	}

	/**
	 * Creates a new relationship from this node to the given pattern.
	 *
	 * @param Pattern $pattern
	 * @return Relationship
	 */
	public function relationshipTo(Pattern $pattern): Relationship
	{
		return new Relationship($this, $pattern, Relationship::DIR_RIGHT);
	}

	/**
	 * Creates a new relationship from the given pattern to this node.
	 *
	 * @param Pattern $pattern
	 * @return Relationship
	 */
	public function relationshipFrom(Pattern $pattern): Relationship
	{
		return new Relationship($this, $pattern, Relationship::DIR_LEFT);
	}

	/**
	 * Creates a new unidirectional relationship between this node and the given pattern.
	 *
	 * @param Pattern $pattern
	 * @return Relationship
	 */
	public function relationshipUni(Pattern $pattern): Relationship
	{
		return new Relationship($this, $pattern, Relationship::DIR_UNI);
	}
}