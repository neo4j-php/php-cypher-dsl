<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This class represents a DELETE clause.
 *
 * The DELETE clause is used to delete graph element — nodes, relationships and paths.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 105)
 * @see Query::delete() for a more convenient method to construct this class
 */
final class DeleteClause extends Clause
{
    /**
     * Whether the DETACH modifier is needed.
     */
    private bool $detach = false;

    /**
     * @var StructuralType[] The structures to delete
     */
    private array $structures = [];

    /**
     * Sets the clause to DETACH DELETE. Without DETACH DELETE,
     * all relationships connected to the nodes/paths need to be explicitly deleted.
     *
     * @param bool $detach Whether to use DETACH DELETE
     */
    public function setDetach(bool $detach = true): self
    {
        $this->detach = $detach;

        return $this;
    }

    /**
     * Add one or more structures to delete.
     *
     * @param Pattern|StructuralType $structures The structures to delete
     */
    public function addStructure(...$structures): self
    {
        $res = [];

        foreach ($structures as $structure) {
            $res[] = CastUtils::toStructuralType($structure);
        }

        $this->structures = array_merge($this->structures, $res);

        return $this;
    }

    /**
     * Returns whether the deletion detaches the relationships.
     */
    public function detachesDeletion(): bool
    {
        return $this->detach;
    }

    /**
     * Returns the structures to delete.
     *
     * @return StructuralType[]
     */
    public function getStructural(): array
    {
        return $this->structures;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        if ($this->detach) {
            return "DETACH DELETE";
        }

        return "DELETE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(static fn (StructuralType $structure) => $structure->toQuery(), $this->structures)
        );
    }
}
