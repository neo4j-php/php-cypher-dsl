<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\PathTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;
use WikibaseSolutions\CypherDSL\Variable;
use function array_key_exists;
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
            if (!array_key_exists($i + 1, $this->nodes)) {
                --$i;
                break;
            }
            $cql .= $this->nodes[$i]->toQuery();
            $cql .= $relationship->toQuery();
        }

        $cql .= $this->nodes[($i ?? -1) + 1]->toQuery();

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

    public function relationshipTo(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_RIGHT, $type, $properties, $name);

        return $this->relationship($relationship, $node);
    }

    public function relationshipFrom(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_LEFT, $type, $properties, $name);

        return $this->relationship($relationship, $node);
    }

    public function relationshipUni(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_UNI, $type, $properties, $name);

        return $this->relationship($relationship, $node);
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
            $relationship->named($name);
        }

        return $relationship;
    }
}
