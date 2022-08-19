<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021- Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
