<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

use function array_key_exists;
use function is_array;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\PathTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Variable;

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
        // If a variable exists, we need to assign following the expression to it
        if ($this->getVariable() !== null) {
            $cql = $this->getName()->toQuery() . ' = ';
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
    public function relationship(RelationshipType $relationship, StructuralType $nodeOrPath): Path
    {
        self::assertClass('nodeOrPath', [__CLASS__, Node::class], $nodeOrPath);

        $this->relationships[] = $relationship;
        if ($nodeOrPath instanceof self) {
            $this->relationships = array_merge($this->relationships, $nodeOrPath->getRelationships());
            $this->nodes = array_merge($this->nodes, $nodeOrPath->getNodes());
        } else {
            $this->nodes []= $nodeOrPath;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function relationshipTo(StructuralType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_RIGHT, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
    }

    /**
     * @inheritDoc
     */
    public function relationshipFrom(StructuralType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_LEFT, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
    }

    /**
     * @inheritDoc
     */
    public function relationshipUni(StructuralType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_UNI, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
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
