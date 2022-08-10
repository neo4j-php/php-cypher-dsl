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
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a CALL {} (subquery) clause. The CALL {} clause evaluates a subquery that returns
 * some values.
 *
 * @note This clause is not officially part of the openCypher standard.
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
    public function withSubQuery(Query $subQuery): self
    {
        $this->subQuery = $subQuery;

        return $this;
    }

    /**
     * Sets the variables to include in the WITH clause. This overwrites any previously set variables.
     *
     * @param Variable[]|string[] $variables A list of variable objects, or strings to cast to variables
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/#subquery-correlated-importing
     */
    public function withVariables(array $variables): self
    {
        $res = [];

        foreach ($variables as $variable) {
            $this->assertClass('variables', [Variable::class, 'string'], $variable);
            $res[] = is_string($variable) ? new Variable($variable) : $variable;
        }

        $this->withVariables = $res;

        return $this;
    }

	/**
	 * Add a variable to include in the WITH clause.
	 *
	 * @param Variable|string $variable A variable or a string to cast to a variable
	 * @return $this
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/#subquery-correlated-importing
	 */
	public function addVariable($variable): self
	{
		$this->assertClass('variable', [Variable::class, 'string'], $variable);
		$this->withVariables[] = is_string($variable) ? new Variable($variable) : $variable;

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
            return '';
        }

        $subQuery = $this->subQuery->build();

        if ($subQuery === '') {
            return '';
        }

        if ($this->withVariables !== []) {
            $withClause = new WithClause();
            $withClause->setEntries($this->withVariables);

            $subQuery = $withClause->toQuery() . ' ' . $subQuery;
        }

        return sprintf('{ %s }', $subQuery);
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'CALL';
    }
}
