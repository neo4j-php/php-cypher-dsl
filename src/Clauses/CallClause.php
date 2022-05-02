<?php

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
     * @var Query The query to call.
     */
    private Query $subQuery;

    /**
     * @param Query $subQuery The query to call.
     */
    public function __construct(Query $subQuery)
    {
        $this->subQuery = $subQuery;
    }

    /**
     * Returns the query that is being called.
     *
     * @return Query
     */
    public function getSubQuery(): Query
    {
        return $this->subQuery;
    }

    public function toQuery(): string
    {
        $subQuery = trim($this->subQuery->toQuery());

        if ($subQuery === '') {
            return '';
        }

        return sprintf('CALL { %s }', $subQuery);
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return '{ ' . $this->subQuery->toQuery() . ' }';
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'CALL';
    }
}
