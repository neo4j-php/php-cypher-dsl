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

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

/**
 * This class represents a CALL (CALL procedure) clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
 */
class CallProcedureClause extends Clause
{
    /**
     * @var string The procedure to call
     */
    private string $procedure;

    /**
     * @var Expression[] The arguments passed to the procedure
     */
    private array $arguments = [];

    /**
     * Sets the procedure to call. This can be for instance "apoc.load.json". This
     * procedure name is passed unescaped to the query.
     *
     * @param  string $procedure
     * @return CallProcedureClause
     */
    public function setProcedure(string $procedure): self
    {
        $this->procedure = $procedure;

        return $this;
    }

    /**
     * Sets the arguments to pass to this procedure call. This overwrites any previously passed
     * arguments.
     *
     * @param  Expression[] $arguments
     * @return CallProcedureClause
     */
    public function withArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Add an argument to pass to this procedure call.
     *
     * @param  Expression $argument
     * @return CallProcedureClause
     */
    public function addArgument(Expression $argument): self
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "CALL";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->procedure)) {
            return "";
        }

        $arguments = implode(
            ", ",
            array_map(fn (Expression $pattern): string => $pattern->toQuery(), $this->arguments)
        );

        return sprintf("%s(%s)", $this->procedure, $arguments);
    }
}