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
use WikibaseSolutions\CypherDSL\Property;

/**
 * This class represents an ORDER BY clause. This clause should always be preceded by a RETURN
 * or WITH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
 */
class OrderByClause extends Clause
{
    use EscapeTrait;

    /**
     * @var Property[] The expressions to include in the clause
     */
    private array $properties = [];

    /**
     * @var bool
     */
    private bool $descending = false;

    /**
     * Add a property to sort on.
     *
     * @param  Property $property The additional property to sort on
     * @return OrderByClause
     */
    public function addProperty(Property $property): self
    {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Set to sort in a DESCENDING order.
     *
     * @param  bool $descending
     * @return OrderByClause
     */
    public function setDescending(bool $descending = true): self
    {
        $this->descending = $descending;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "ORDER BY";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        $properties = array_map(fn (Property $property): string => $property->toQuery(), $this->properties);
        $subject = implode(", ", $properties);

        return $this->descending ? sprintf("%s DESCENDING", $subject) : $subject;
    }
}