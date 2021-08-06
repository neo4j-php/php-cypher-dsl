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

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Encoder;

/**
 * This class represents an arbitrary relationship between two nodes, a node and a
 * relationship or between two relationships.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
 * @package WikibaseSolutions\CypherDSL
 */
class Relationship implements Pattern
{
	use Encoder;

	const DIR_RIGHT = ["-", "->"];
	const DIR_LEFT = ["<-", "-"];
	const DIR_UNI = ["-", "-"];

	/**
	 * @var Pattern The pattern left of the relationship
	 */
	private Pattern $a;

	/**
	 * @var Pattern The pattern right of the relationship
	 */
	private Pattern $b;

	/**
	 * @var array The direction of the relationship
	 */
	private array $direction;

	/**
	 * @var string
	 */
	private string $variable = "";

	/**
	 * @var array
	 */
	private array $types = [];

	/**
	 * @var array
	 */
	private array $properties = [];

	/**
	 * Relationship constructor.
	 *
	 * @param Pattern $a The node left of the relationship
	 * @param Pattern $b The node right of the relationship
	 * @param array $direction The direction of the relationship, should be either:
	 *
	 * - Relationship::DIR_RIGHT (for a relation of (a)-->(b))
	 * - Relationship::DIR_LEFT (for a relation of (a)<--(b))
	 * - Relationship::DIR_UNI (for a relation of (a)--(b))
	 */
	public function __construct(Pattern $a, Pattern $b, array $direction)
	{
		$this->a = $a;
		$this->b = $b;

		if ($direction !== self::DIR_RIGHT && $direction !== self::DIR_LEFT && $direction !== self::DIR_UNI) {
			throw new InvalidArgumentException("The direction must be either 'DIR_LEFT', 'DIR_RIGHT' or 'RELATED_TO'");
		}

		$this->direction = $direction;
	}

	/**
	 * @param string $variable
	 * @return Relationship
	 */
	public function named(string $variable): self
	{
		$this->variable = $variable;

		return $this;
	}

	/**
	 * @param string $type
	 * @return Relationship
	 */
	public function withType(string $type): self
	{
		$this->types[] = $type;

		return $this;
	}

	/**
	 * @param array $properties
	 * @return Relationship
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
		$a = $this->a->toString();
		$b = $this->b->toString();

		return $a . $this->direction[0] . $this->conditionToString() . $this->direction[1] . $b;
	}

	/**
	 * @return string
	 */
	private function conditionToString(): string
	{
		$escapedVariable = $this->escapeName($this->variable);

		$types = array_filter($this->types);
		$properties = array_filter($this->properties);

		if (count($types) !== 0) {
			$escapedTypes = array_map(fn (string $type): string => $this->escapeName($type), $types);
			$typeCondition = sprintf(":%s", implode("|", $escapedTypes));
		} else {
			$typeCondition = "";
		}

		if (count($properties) !== 0) {
			$propertyList = $this->encodePropertyList($properties);

			if ($typeCondition !== "" || $escapedVariable !== "") {
				// Add some padding between the property list and the preceding structure
				$propertyList = " " . $propertyList;
			}
		} else {
			$propertyList = "";
		}

		return sprintf("[%s%s%s]", $escapedVariable, $typeCondition, $propertyList);
	}
}