<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

/**
 * This class represents the union clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/union/
 */
class UnionClause extends Clause
{
    /** @var bool Whether the union should include all results or remove the duplicates instead. */
    private bool $all;

    /**
     * @param bool $all Whether the union should include all results or remove the duplicates instead.
     */
    public function __construct(bool $all = false)
    {
        $this->all = $all;
    }

    /**
     * Returns whether the union includes all results or removes the duplicates instead.
     *
     * @return bool
     */
    public function includesAll(): bool
    {
        return $this->all;
    }

    /**
     * @inheritDoc
     */
    public function canBeEmpty(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return 'UNION';
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return $this->all ? 'ALL' : '';
    }
}