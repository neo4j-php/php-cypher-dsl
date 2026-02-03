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

use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;

/**
 * This class represents a MATCH clause.
 *
 * The MATCH clause is used to search for the pattern described in it.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 57)
 * @see Query::match() for a more convenient method to construct this class
 */
final class MatchClause extends Clause
{
    /**
     * @var CompletePattern[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add one or more patterns to the MATCH clause.
     */
    public function addPattern(CompletePattern ...$pattern): self
    {
        $this->patterns = array_merge($this->patterns, $pattern);

        return $this;
    }

    /**
     * Returns the patterns to match.
     *
     * @return CompletePattern[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "MATCH";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(static fn (CompletePattern $pattern): string => $pattern->toQuery(), $this->patterns)
        );
    }
}
