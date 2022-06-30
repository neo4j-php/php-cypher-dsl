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

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Variable;

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
     * The variables that need to be deleted.
     *
     * @var Variable[] $variables
     */
    private array $variables = [];

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
     * @param Variable[] $variables The variables to delete
     * @return $this
     */
    public function setVariables(array $variables): self
    {
        foreach ($variables as $variable) {
            $this->assertClass('variables', Variable::class, $variable);
        }

        $this->variables = $variables;

        return $this;
    }

    /**
     * Add the variable to be deleted. Unlike DeleteClause::setVariables(), this function adds the variable and does not
     * override previous variables.
     *
     * @param Variable $variable The name of the object that should be deleted
     * @return $this
     */
    public function addVariable(Variable $variable): self
    {
        $this->variables[] = $variable;

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
     * Returns the variables to delete.
     *
     * @return Variable[]
     */
    public function getVariables(): array
    {
        return $this->variables;
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
            array_map(fn (Variable $variable) => $variable->toQuery(), $this->variables)
        );
    }
}
