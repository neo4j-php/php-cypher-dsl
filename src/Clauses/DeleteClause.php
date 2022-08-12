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

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This class represents a DELETE clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
 */
class DeleteClause extends Clause
{
    use ErrorTrait;

    /**
     * Whether the DETACH modifier is needed.
     *
     * @var bool $detach
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
     * @return $this
     */
    public function setDetach(bool $detach = true): self
    {
        $this->detach = $detach;

        return $this;
    }

    /**
     * Sets the structures to be deleted. This overwrites previous structures if they exist.
     *
     * @param StructuralType[] $structures The structures to delete
     * @return $this
     */
    public function setStructures(array $structures): self
    {
        foreach ($structures as $structure) {
            $this->assertClass('structure', StructuralType::class, $structure);
        }

        $this->structures = $structures;

        return $this;
    }

    /**
     * Add a structure to be deleted.
     *
     * @param StructuralType $structure The structure that should be deleted
     * @return $this
     */
    public function addStructure(StructuralType $structure): self
    {
        $this->structures[] = $structure;

        return $this;
    }

    /**
     * Returns whether the deletion detaches the relationships.
     *
     * @return bool
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
    public function getStructures(): array
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
            array_map(fn (StructuralType $structure) => $structure->toQuery(), $this->structures)
        );
    }
}
