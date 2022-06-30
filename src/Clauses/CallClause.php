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

use WikibaseSolutions\CypherDSL\Query;

/**
 * This class represents a CALL clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/
 */
class CallClause extends Clause
{
    /**
     * @var Query|null The query to call
     */
    private ?Query $subQuery = null;

    /**
     * Sets the query to call
     *
     * @param Query $subQuery
     * @return $this
     */
    public function setSubQuery(Query $subQuery): self
    {
        $this->subQuery = $subQuery;

        return $this;
    }

    /**
     * Returns the query that is being called.
     *
     * @return Query|null
     */
    public function getSubQuery(): ?Query
    {
        return $this->subQuery;
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->subQuery)) {
            return "";
        }

        $subQuery = $this->subQuery->build();

        if ($subQuery === "") {
            return "";
        }

        return sprintf("{ %s }", trim($this->subQuery->build()));
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'CALL';
    }
}
