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

use WikibaseSolutions\CypherDSL\QueryConvertible;

/**
 * This class represents all the clauses in the Cypher query language.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/
 */
abstract class Clause implements QueryConvertible
{
	/**
	 * Returns whether this clause is still valid if it has an empty subject.
	 *
	 * @return bool
	 */
	public function canBeEmpty(): bool
	{
		return false;
	}

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
		if ($this->getClause() === "") {
			// If we have an empty clause (for example, for RAW queries), return nothing at all
			return "";
		}

        if ($this->getSubject() === "") {
            // If we have an empty subject, either return the empty clause, or nothing at all
            return $this->canBeEmpty() ? $this->getClause() : "";
        }

        return sprintf("%s %s", $this->getClause(), $this->getSubject());
    }

    /**
     * Returns the subject of this object. The subject is anything after
     * the clause. For example, in the partial query "MATCH (a)", the subject
     * would be "(a)".
     *
     * @return string
     */
    abstract protected function getSubject(): string;

    /**
     * Returns the clause this object describes. For instance "MATCH".
     *
     * @return string
     */
    abstract protected function getClause(): string;
}
