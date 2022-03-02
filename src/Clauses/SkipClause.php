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

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This class represents a SKIP clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/skip/
 */
class SkipClause extends Clause
{
    /**
     * The expression of the SKIP statement.
     *
     * @var NumeralType|null $skip
     */
    private ?NumeralType $skip = null;

    /**
     * Sets the expression that returns the skip.
     *
     * @param NumeralType $skip The amount to skip
     * @return SkipClause
     */
    public function setSkip(NumeralType $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Returns the amount to skip.
     *
     * @return NumeralType|null
     */
    public function getSkip(): ?NumeralType
    {
        return $this->skip;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "SKIP";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (isset($this->skip)) {
            return $this->skip->toQuery();
        }

        return "";
    }
}