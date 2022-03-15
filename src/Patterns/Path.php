<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\PathTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;
use WikibaseSolutions\CypherDSL\Variable;
use function is_array;

class Path implements PathType
{
    use PathTrait;
    use ErrorTrait;

    /** @var Relationship[] */
    private array $relationships;
    /** @var Node[] */
    private array $nodes;

    /**
     * @param Node|Node[] $nodes
     * @param Relationship|Relationship[] $relationships
     */
    public function __construct($nodes, $relationships = null)
    {
        self::assertClassOrType('relationships', [Relationship::class, 'array'], $relationships);
        self::assertClassOrType('nodes', [Node::class, 'array'], $nodes);

        $this->nodes = is_array($nodes) ? array_values($nodes) : [$nodes];
        $this->relationships = is_array($relationships) ? array_values($relationships) : [$relationships];
    }

    public function toQuery(): string
    {
        if (count($this->nodes) === 0) {
            return '';
        }

        $cql = '';
        if ($this->getVariable() !== null) {
            $cql = $this->getName()->toQuery() . ' = ';
        }

        foreach ($this->relationships as $i => $relationship) {
            if (count($this->nodes) <= $i) {
                break;
            }
            $cql .= $this->nodes[$i]->toQuery();
            $cql .= $relationship->toQuery();
        }

        $cql .= $this->nodes[$i ?? 0]->toQuery();

        return $cql;
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

    public function relationship(RelationshipType $relationship, NodeType $node): Path
    {
        $this->relationships[] = $relationship;
        $this->nodes[] = $node;

        return $this;
    }

    public function relationshipTo(NodeType $node, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_RIGHT, $properties, $name);

        return $this->relationship($relationship, $node);
    }

    public function relationshipFrom(NodeType $node, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_LEFT, $properties, $name);

        return $this->relationship($relationship, $node);
    }

    public function relationshipUni(NodeType $node, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_UNI, $properties, $name);

        return $this->relationship($relationship, $node);
    }

    /**
     * @param array $direction
     * @param mixed $properties
     * @param mixed $name
     *
     * @return Relationship
     */
    private function buildRelationship(array $direction,  $properties, $name): Relationship
    {
        self::assertClassOrType('properties', ['array', PropertyMap::class, null], $properties);
        self::assertClassOrType('name', ['string', Variable::class, null], $name);

        $relationship = new Relationship($direction);
        if ($properties !== null) {
            $relationship->withProperties($properties);
        }
        if ($name !== null) {
            $relationship->named($name);
        }
        return $relationship;
    }
}