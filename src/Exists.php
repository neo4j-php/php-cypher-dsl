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

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents the EXISTS expression.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#filter-on-relationship-type
 */
class Exists implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var MatchClause The MATCH part of the EXISTS expression
     */
    private MatchClause $match;

    /**
     * @var WhereClause|null The optional WHERE part of the EXISTS expression
     */
    private ?WhereClause $where;

    /**
     * Exists constructor.
     *
     * @param MatchClause $match The MATCH part of the EXISTS expression
     * @param ?WhereClause $where The optional WHERE part of the EXISTS expression
     */
    public function __construct(MatchClause $match, ?WhereClause $where = null)
    {
        $this->match = $match;
        $this->where = $where;
    }

    /**
     * Returns the MATCH part of the EXISTS expression.
     *
     * @return MatchClause
     */
    public function getMatch(): MatchClause
    {
        return $this->match;
    }

    /**
     * Returns the WHERE part of the expression.
     *
     * @return WhereClause|null
     */
    public function getWhere(): ?WhereClause
    {
        return $this->where;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        if (isset($this->where)) {
            return sprintf("EXISTS { %s %s }", $this->match->toQuery(), $this->where->toQuery());
        } else {
            return sprintf("EXISTS { %s }", $this->match->toQuery());
        }
    }
}