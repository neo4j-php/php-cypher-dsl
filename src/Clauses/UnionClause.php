<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Query;

/**
 * This class represents the UNION clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/union/
 */
class UnionClause extends Clause
{
    /**
	 * @var bool Whether the union should include all results or remove the duplicates instead.
	 */
    private bool $all = false;

	/**
	 * Sets that the union should include all results, instead of removing duplicates.
	 *
	 * @param bool $all Whether the union should include all results or remove the duplicates instead
	 * @return static
	 */
	public function setAll(bool $all = true): self
	{
		$this->all = $all;

		return $this;
	}

    /**
     * Combines two queries with a union.
     *
     * @param Query $left The query preceding the union clause.
     * @param Query $right The query after the union clause.
     * @param bool $all Whether the union should include all results or remove the duplicates instead.
	 *
	 * TODO: Move this function somewhere else.
     */
    public static function union(Query $left, Query $right, bool $all = false): Query
    {
        $tbr = Query::new();

        foreach ($left->getClauses() as $clause) {
            $tbr->addClause($clause);
        }

        $unionClause = new self();
        $unionClause->setAll($all);

        $tbr->addClause($unionClause);

        foreach ($right->getClauses() as $clause) {
            $tbr->addClause($clause);
        }

        return $tbr;
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
        return $this->all ? 'ALL' : '';
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return 'UNION';
    }
}
