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

use WikibaseSolutions\CypherDSL\Patterns\MatchablePattern;

/**
 * This class represents a MATCH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 57)
 * @see Query::match() for a more convenient method to construct this class
 */
final class MatchClause extends Clause
{
    /**
     * @var MatchablePattern[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add one or more patterns to the MATCH clause.
     *
     * @param MatchablePattern ...$pattern
     * @return $this
     */
    public function addPattern(MatchablePattern ...$pattern): self
    {
        $this->patterns = array_merge($this->patterns, $pattern);

        return $this;
    }

    /**
     * Returns the patterns to match.
     *
     * @return MatchablePattern[]
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
            array_map(fn (MatchablePattern $pattern): string => $pattern->toQuery(), $this->patterns)
        );
    }
}
