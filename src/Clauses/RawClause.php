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

/**
 * Represents a raw clause.
 */
class RawClause extends Clause
{
	/**
	 * @var string The name of the clause; for instance "MATCH"
	 */
	private string $clause;

	/**
	 * @var string The subject/body of the clause
	 */
	private string $subject;

	/**
	 * RawClause constructor.
	 *
	 * @param string $clause The name of the clause; for instance "MATCH"
	 * @param string $subject The subject/body of the clause
	 */
	public function __construct(string $clause, string $subject)
	{
		$this->clause = $clause;
		$this->subject = $subject;
	}

	/**
	 * @inheritDoc
	 */
	protected function getClause(): string
	{
		return $this->clause;
	}

	/**
	 * @inheritDoc
	 */
	protected function getSubject(): string
	{
		return $this->subject;
	}
}