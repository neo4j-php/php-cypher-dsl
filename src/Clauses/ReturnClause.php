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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\HelperTraits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents a RETURN clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
 */
class ReturnClause extends Clause
{
    use EscapeTrait;

    /**
     * @var bool Whether to be a RETURN DISTINCT query
     */
    private bool $distinct = false;

    /**
     * @var AnyType[] The expressions to return
     */
    private array $columns = [];

    /**
     * Sets this query to only retrieve unique rows.
     *
     * @see    https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-unique-results
     * @param bool $distinct
     * @return ReturnClause
     */
    public function setDistinct(bool $distinct = true): self
    {
        $this->distinct = $distinct;

        return $this;
    }

    /**
     * Returns the columns to return. Aliased columns have string keys instead of integers.
     *
     * @return AnyType[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns whether the returned results are distinct.
     *
     * @return bool
     */
    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * Add a new column to this RETURN clause.
     *
     * @param AnyType $column The expression to return
     * @param string $alias The alias of this column
     *
     * @return ReturnClause
     * @see    https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
     */
    public function addColumn(AnyType $column, string $alias = ""): self
    {
        if ($alias !== "") {
            $this->columns[$alias] = $column;
        } else {
            $this->columns[] = $column;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return $this->distinct ?
            "RETURN DISTINCT" :
            "RETURN";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        $expressions = [];

        foreach ($this->columns as $alias => $expression) {
            $expressionQuery = $expression->toQuery();
            $expressions[] = is_int($alias) ?
                $expressionQuery :
                sprintf("%s AS %s", $expressionQuery, $this->escape($alias));
        }

        return implode(", ", $expressions);
    }
}
