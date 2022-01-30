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

use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents a WITH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
 */
class WithClause extends Clause
{
    use EscapeTrait;

    /**
     * @var array The expressions to include in the clause
     */
    private array $expressions = [];

    /**
     * Add a new entry to the WITH clause.
     *
     * @param AnyType $expression The entry to add
     * @param string $alias An optional entry alias
     *
     * @return WithClause
     */
    public function addEntry(AnyType $expression, string $alias = ""): self
    {
        if ($alias !== "") {
            $this->expressions[$alias] = $expression;
        } else {
            $this->expressions[] = $expression;
        }

        return $this;
    }

    /**
     * Returns the expression to include in the clause.
     *
     * @return array
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
        return "WITH";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        $expressions = [];

        foreach ($this->expressions as $alias => $expression) {
            $expressionQuery = $expression->toQuery();
            $expressions[] = is_int($alias) ?
                $expressionQuery :
                sprintf("%s AS %s", $expressionQuery, $this->escape($alias));
        }

        return implode(", ", $expressions);
    }
}