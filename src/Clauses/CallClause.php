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

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;

/**
 * This class represents a CALL {} (subquery) clause. The CALL {} clause evaluates a subquery that returns
 * some values.
 *
 * @note This clause is not part of the openCypher standard.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/
 * @see Query::call() for a more convenient method to construct this class
 */
class CallClause extends Clause
{
	use ErrorTrait;

    /**
     * @var Query|null The subquery to call, or NULL if no subquery has been set yet
     */
    private ?Query $subQuery = null;

	/**
	 * @var Variable[] The variables to include in the WITH clause (for correlated queries)
	 */
	private array $withVariables = [];

    /**
     * Sets the query to call. This overwrites any previously set subquery.
     *
     * @param Query $subQuery The subquery to call
     * @return $this
     */
    public function setSubQuery(Query $subQuery): self
    {
        $this->subQuery = $subQuery;

        return $this;
    }

	/**
	 * Sets the variables to include in the WITH clause. This overwrites any previously set variables.
	 *
	 * @param Variable[] $variables
	 * @return $this
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/#subquery-correlated-importing
	 */
	public function setVariables(array $variables): self
	{
		foreach ($variables as $variable) {
			$this->assertClass('variables', Variable::class, $variable);
		}

		$this->withVariables = $variables;

		return $this;
	}

	/**
	 * Add a variable to include in the WITH clause.
	 *
	 * @param Variable $variable
	 * @return $this
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/#subquery-correlated-importing
	 */
	public function addVariable(Variable $variable): self
	{
		$this->withVariables[] = $variable;

		return $this;
	}

    /**
     * Returns the query that is being called. This query does not include the WITH clause that is inserted
	 * if there are any correlated variables.
     *
     * @return Query|null
     */
    public function getSubQuery(): ?Query
    {
        return $this->subQuery;
    }

	/**
	 * Returns the variables that will be included in the WITH clause.
	 *
	 * @return Variable[]
	 */
	public function getWithVariables(): array
	{
		return $this->withVariables;
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

		if ($this->withVariables !== []) {
			$withClause = new WithClause();
			$withClause->setEntries($this->withVariables);

			return sprintf("{ %s %s }", $withClause->toQuery(), trim($this->subQuery->build()));
		} else {
			return sprintf("{ %s }", trim($this->subQuery->build()));
		}
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'CALL';
    }
}
