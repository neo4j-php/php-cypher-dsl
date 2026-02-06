<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * This class represents a path, which is an alternating sequence of nodes and relationships.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types Corresponding documentation on Neo4j.com
 */
final class Path implements BooleanType, CompletePattern, RelatablePattern
{
    use BooleanTypeTrait;
    use PatternTrait;

    /**
     * @var Relationship[]
     */
    private array $relationships;

    /**
     * @var Node[]
     */
    private array $nodes;

    /**
     * @param Node|Node[]                 $nodes
     * @param Relationship|Relationship[] $relationships
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(Node|array $nodes = [], Relationship|array $relationships = [])
    {
        $nodes = is_array($nodes) ? $nodes : [$nodes];
        $relationships = is_array($relationships) ? $relationships : [$relationships];

        $this->nodes = $nodes;
        $this->relationships = $relationships;
    }

    /**
     * Returns the relatables in the path in sequential order.
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
    public function relationship(Relationship $relationship, Pattern $pattern): self
    {
        $this->relationships[] = $relationship;

        if ($pattern instanceof self) {
            // If the given relatable is also a path, we can merge their relatables and relationships
            $this->relationships = array_merge($this->relationships, $pattern->getRelationships());
            $this->nodes = array_merge($this->nodes, $pattern->getNodes());

            return $this;
        }
        // Otherwise, add the relatable to the list of nodes
        // @phpstan-ignore-next-line
        $this->nodes[] = $pattern;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function relationshipTo(Pattern $pattern, ?string $type = null, MapType|array|null $properties = null, Variable|string|null $name = null): self
    {
        return $this->relationship(
            self::buildRelationship(Direction::RIGHT, $type, $properties, $name),
            $pattern
        );
    }

    /**
     * @inheritDoc
     */
    public function relationshipFrom(Pattern $pattern, ?string $type = null, MapType|array|null $properties = null, Variable|string|null $name = null): self
    {
        return $this->relationship(
            self::buildRelationship(Direction::LEFT, $type, $properties, $name),
            $pattern
        );
    }

    /**
     * @inheritDoc
     */
    public function relationshipUni(Pattern $pattern, ?string $type = null, MapType|array|null $properties = null, Variable|string|null $name = null): self
    {
        return $this->relationship(
            self::buildRelationship(Direction::UNI, $type, $properties, $name),
            $pattern
        );
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
        // path as described in page 66 of the openCypher reference (version 9).
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
     * Construct a new relationship from the given parameters.
     *
     * @param Direction            $direction  The direction of the relationship
     * @param null|string          $type       The type of the relationship
     * @param null|array|MapType   $properties The properties to add to the relationship
     * @param null|string|Variable $name       The name of the variable to which to assign this relationship
     */
    private static function buildRelationship(Direction $direction, ?string $type = null, MapType|array|null $properties = null, Variable|string|null $name = null): Relationship
    {
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
