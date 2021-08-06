<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

abstract class Clause
{
	/**
	 * @return string
	 */
	public function toString(): string
	{
		return sprintf("%s %s", $this->getClause(), $this->getSubject());
	}

	/**
	 * Returns the clause this object describes. For instance "MATCH".
	 *
	 * @return string
	 */
	abstract public function getClause(): string;

	/**
	 * Returns the subject of this object. The subject is anything after
	 * the clause. For example, in the partial query "MATCH (a)", the subject
	 * would be "(a)".
	 *
	 * @return string
	 */
	abstract public function getSubject(): string;
}