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

use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\AndOperator;
use WikibaseSolutions\CypherDSL\Expressions\Not;
use WikibaseSolutions\CypherDSL\Expressions\OrOperator;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\XorOperator;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * This class represents a path which is an alternating sequence of nodes and relationships.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 5)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
class Path extends Pattern implements BooleanType, Relatable
{
	use BooleanTypeTrait;

    use ErrorTrait;

    /**
	 * @var Relationship[]
	 */
    private array $relationships;

    /**
	 * @var Relatable[]
	 */
    private array $relatables;

    /**
     * @param Node|Node[] $nodes
     * @param Relationship|Relationship[] $relationships
     */
    public function __construct($nodes = [], $relationships = [])
    {
        self::assertClass('nodes', [Node::class, 'array'], $nodes);
        self::assertClass('relationships', [Relationship::class, 'array'], $relationships);

        $this->relatables = is_array($nodes) ? $nodes : [$nodes];
        $this->relationships = is_array($relationships) ? $relationships : [$relationships];
    }

    /**
     * Returns the nodes in the path in sequential order.
     *
     * @return Node[]
     */
    public function getRelatables(): array
    {
        return $this->relatables;
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
        if (count($this->relatables) === 0) {
            return '';
        }

        $cql = '';

        // If a variable exists, we need to assign following the expression to it; this results in a named
		// path as described in page 66 of the openCypher reference (version 9). This is semantically
		// different from an assignment.
        if (isset($this->variable)) {
            $cql = $this->variable->toQuery() . ' = ';
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
            if (!array_key_exists($i + 1, $this->relatables)) {
                --$i;

                break;
            }
            $cql .= $this->relatables[$i]->toQuery();
            $cql .= $relationship->toQuery();
        }

        // Since the iteration leaves us at the final relationship, we still need to add the final node.
        // If the path is simply a single node, $i won't be defined, hence the null coalescing operator with -1. By
        // coalescing with -1 instead of 0, we remove the need of a separate if check, making both cases valid when they
        // are incremented by 1.
        $cql .= $this->relatables[($i ?? -1) + 1]->toQuery();

        return $cql;
    }

    /**
     * @inheritDoc
     */
	public function relationship(Relationship $relationship, Relatable $relatable): Path
	{
		$this->relationships[] = $relationship;

		if ($relatable instanceof Path) {
            // If the given relatable is also a path, we can merge their relatables and relationships
			$this->relationships = array_merge($this->relationships, $relatable->getRelationships());
			$this->relatables = array_merge($this->relatables, $relatable->getRelatables());
		} else {
            // Otherwise, add the relatable to the list of relatables
			$this->relatables[] = $relatable;
		}

		return $this;
	}

    /**
     * @inheritDoc
     */
	public function relationshipTo(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return $this->relationship(
			self::buildRelationship(Relationship::DIR_RIGHT, $type, $properties, $name),
            $relatable
        );
	}

	/**
	 * @inheritDoc
	 */
	public function relationshipFrom(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return $this->relationship(
			self::buildRelationship(Relationship::DIR_LEFT, $type, $properties, $name),
            $relatable
        );
	}

    /**
     * @inheritDoc
     */
	public function relationshipUni(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return $this->relationship(
            self::buildRelationship(Relationship::DIR_UNI, $type, $properties, $name),
            $relatable
        );
	}

    /**
     * @param array $direction
     * @param string|null $type
     * @param mixed $properties
     * @param mixed $name
     *
     * @return Relationship
     */
    private static function buildRelationship(array $direction, ?string $type, $properties, $name): Relationship
    {
        self::assertClass('properties', [MapType::class, 'array', 'null'], $properties);
        self::assertClass('name', [Variable::class, 'string', 'null'], $name);

        $relationship = new Relationship($direction);

        if ($type !== null) {
            $relationship->addType($type);
        }

        if ($properties !== null) {
            $relationship->withProperties($properties);
        }

        if ($name !== null) {
            $relationship->withVariable($name);
        }

        return $relationship;
    }
}
