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
    private Query $query;

    /**
     * @param Query $query The query to call.
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function toQuery(): string
    {
        return sprintf('CALL { %s }', $this->getSubject());
    }

    /**
     * Returns the query that is being called.
     *
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return $this->query->toQuery();
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'CALL';
    }
}