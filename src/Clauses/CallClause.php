<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a CALL {} (subquery) clause. The CALL {} clause evaluates a subquery that returns
 * some values.
 *
 * @note This feature is not part of the openCypher standard. For more information, see https://github.com/opencypher/openCypher/blob/a507292d35280aca9e37bf938cdec4fdd1e64ba9/docs/standardisation-scope.adoc.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/
 * @see Query::call() for a more convenient method to construct this class
 */
final class CallClause extends Clause
{
    use CastTrait;
    use ErrorTrait;

    /**
     * @var null|Query The sub-query to call, or NULL if no sub-query has been set yet
     */
    private ?Query $subQuery = null;

    /**
     * @var Variable[] The variables to include in the WITH clause (for correlated queries)
     */
    private array $withVariables = [];

    /**
     * Sets the query to call. This overwrites any previously set sub-query.
     *
     * @param Query $subQuery The sub-query to call
     *
     * @return $this
     */
    public function withSubQuery(Query $subQuery): self
    {
        $this->subQuery = $subQuery;

        return $this;
    }

    /**
     * Add one or more variables to include in the WITH clause.
     *
     * @param Pattern|string|Variable ...$variables
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/#subquery-correlated-importing
     */
    public function addWithVariable(...$variables): self
    {
        $res = [];

        foreach ($variables as $variable) {
            $res[] = self::toVariable($variable);
        }

        $this->withVariables = array_merge($this->withVariables, $res);

        return $this;
    }

    /**
     * Returns the query that is being called. This query does not include the WITH clause that is inserted
     * if there are any correlated variables.
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
            $subQuery = Query::new()->with($this->withVariables)->toQuery() . ' ' . $subQuery;
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
