<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Traits\PathTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

class Path implements PathType
{
    use PathTrait;

    /** @var Relationship[] */
    private array $relationships = [];

    public function __construct()
    {
    }

    public function toQuery(): string
    {
        $cql = '';
        if ($this->getVariable() !== null) {
            $cql = $this->getName()->toQuery() . ' = ';
        }

        foreach ($this->relationships as $relationship) {
            $cql .= $relationship->getLeft()->toQuery();
            $cql .= $relationship->toRelationshipQuery();
        }

        if (isset($relationship)) {
            $cql .= $relationship->getRight()->toQuery();
        }

        return $cql;
    }

    public function relationshipTo(StructuralType $pattern): Path
    {
        $this->relationships[] = $this->getLatestNode()->relationshipUni($pattern);

        return $this;
    }

    public function relationshipFrom(StructuralType $pattern): Path
    {
        $this->relationships[] = $this->getLatestNode()->relationshipUni($pattern);

        return $this;
    }

    public function relationshipUni(StructuralType $pattern): Path
    {
        $this->relationships[] = $this->getLatestNode()->relationshipUni($pattern);

        return $this;
    }

    private function getLatestNode(): StructuralType
    {
        return $this->relationships[count($this->relationships) - 1]->getRight();
    }
}