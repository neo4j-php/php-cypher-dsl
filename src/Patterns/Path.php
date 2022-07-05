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
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\VariableTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * This class represents a path which is an alternating sequence of nodes and relationships.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 5)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
class Path implements QueryConvertible
{
    use ErrorTrait;
	use VariableTrait;

    /**
	 * @var Relationship[]
	 */
    private array $relationships;

    /**
	 * @var Node[]
	 */
    private array $nodes;

    /**
     * @param AnyType|AnyType[]|null $nodes
     * @param Relationship|Relationship[]|null $relationships
     */
    public function __construct($nodes = null, $relationships = null)
    {
        self::assertClass('relationships', [Relationship::class, 'array', 'null'], $relationships);
        self::assertClass('nodes', [AnyType::class, 'array', 'null'], $nodes);

        $nodes ??= [];
        $relationships ??= [];

        $this->nodes = is_array($nodes) ? array_values($nodes) : [$nodes];
        $this->relationships = is_array($relationships) ? array_values($relationships) : [$relationships];
    }

    /**
     * Returns the nodes in the path in sequential order.
     *
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * Returns the relationships in the path in sequential order.
     *
     * @return Relationship[]
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        // If there are no nodes in the path, it must be empty.
        if (count($this->nodes) === 0) {
            return '';
        }

        $cql = '';
        // If a variable exists, we need to assign following the expression to it; this results in a named
		// path as described in page 66 of the openCypher reference (version 9). This is semantically
		// different from an assignment.
        if ($this->hasVariable()) {
            $cql = $this->getVariable()->toQuery() . ' = ';
        }

        // We use the relationships as a reference point to iterate over.
        // The nodes position will be calculated using the index of the current relationship.
        // If R is the position of the relationship, N is the position of te node, and x the amount of relationships
        // in the path, then the positional structure is like this:
        // N0 - R0 - N1 - R1 - N2 - ... - Nx - Rx - N(x + 1)
        foreach ($this->relationships as $i => $relationship) {
            // To avoid a future crash, we already look for the node at the end of the relationship.
            // If the following node does not exist, we must break the loop early.
            // This case will be triggered if the amount of nodes is equal or less than the amount of relationships
            // and is thus very unlikely.
            if (!array_key_exists($i + 1, $this->nodes)) {
                --$i;

                break;
            }
            $cql .= $this->nodes[$i]->toQuery();
            $cql .= $relationship->toQuery();
        }

        // Since the iteration leaves us at the final relationship, we still need to add the final node.
        // If the path is simply a single node, $i won't be defined, hence the null coalescing operator with -1. By
        // coalescing with -1 instead of 0, we remove the need of a separate if check, making both cases valid when they
        // are incremented by 1.
        $cql .= $this->nodes[($i ?? -1) + 1]->toQuery();

        return $cql;
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
		self::assertClass('nodeOrPath', [__CLASS__, Node::class], $relatable);

		$this->relationships[] = $relationship;
		if ($relatable instanceof self) {
			$this->relationships = array_merge($this->relationships, $relatable->getRelationships());
			$this->nodes = array_merge($this->nodes, $relatable->getNodes());
		} else {
			$this->nodes []= $relatable;
		}

		return $this;
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
		$relationship = $this->buildRelationship(Relationship::DIR_RIGHT, $type, $properties, $name);

		return $this->relationship($relationship, $relatable);
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
		$relationship = $this->buildRelationship(Relationship::DIR_LEFT, $type, $properties, $name);

		return $this->relationship($relationship, $relatable);
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
		$relationship = $this->buildRelationship(Relationship::DIR_UNI, $type, $properties, $name);

		return $this->relationship($relationship, $relatable);
	}

    /**
     * @param array $direction
     * @param string|null $type
     * @param mixed $properties
     * @param mixed $name
     *
     * @return Relationship
     */
    private function buildRelationship(array $direction, ?string $type, $properties, $name): Relationship
    {
        self::assertClass('properties', ['array', PropertyMap::class, 'null'], $properties);
        self::assertClass('name', ['string', Variable::class, 'null'], $name);

        $relationship = new Relationship($direction);

        if ($type !== null) {
            $relationship->withType($type);
        }

        if ($properties !== null) {
            $relationship->withProperties($properties);
        }

        if ($name !== null) {
            $relationship->setVariable($name);
        }

        return $relationship;
    }
}
