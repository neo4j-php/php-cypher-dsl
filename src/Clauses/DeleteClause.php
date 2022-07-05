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

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;

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
	 * @var NodeType[] The nodes to delete
	 */
	private array $nodes = [];

	/**
     * Sets the clause to DETACH DELETE. Without DETACH DELETE, all relationships need to be explicitly
     * deleted.
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
     * Sets the variables to be deleted. This overwrites previous variables if they exist.
     *
     * @param NodeType[] $nodes The nodes to delete
     * @return $this
     */
    public function setNodes(array $nodes): self
    {
        foreach ($nodes as $node) {
            $this->assertClass('nodes', NodeType::class, $node);
        }

        $this->nodes = $nodes;

        return $this;
    }

    /**
     * Add a node to be deleted.
     *
     * @param NodeType $node The node that should be deleted
     * @return $this
     */
    public function addNode(NodeType $node): self
    {
        $this->nodes[] = $node;

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
     * Returns the nodes to delete.
     *
     * @return NodeType[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
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
            array_map(fn (Variable $variable) => $variable->toQuery(), $this->nodes)
        );
    }
}
