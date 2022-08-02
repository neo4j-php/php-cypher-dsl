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

use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a SET clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
 */
class SetClause extends Clause
{
    use ErrorTrait;

    /**
     * @var PropertyReplacement[]|Label[] $expressions The expressions to set
     */
    private array $expressions = [];

    /**
     * Sets the assignments of this SET clause. This will overwrite any previously added assignments.
     *
     * @param (Assignment|Label)[] $expressions The assignments to execute
     * @return SetClause
     */
    public function setAssignments(array $expressions): self
    {
        foreach ($expressions as $expression) {
            $this->assertClass('expressions', [PropertyReplacement::class, Label::class], $expression);
        }

        $this->expressions = $expressions;

        return $this;
    }

    /**
     * Add an assignment.
     *
     * @param PropertyReplacement|Label $expression The assignment to execute
     * @return SetClause
     */
    public function addAssignment($expression): self
    {
        $this->assertClass('expression', [PropertyReplacement::class, Label::class], $expression);
        $this->expressions[] = $expression;

        return $this;
    }

    /**
     * Returns the expressions to SET.
     *
     * @return PropertyReplacement[]|Label[]
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "SET";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(fn (QueryConvertible $expression): string => $expression->toQuery(), $this->expressions)
        );
    }
}
