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
 * This class represents an OPTIONAL MATCH clause.
 *
 * The OPTIONAL MATCH clause is used to search for the pattern described in it, while using nulls for missing parts
 * of the pattern.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 69)
 */
final class OptionalMatchClause extends Clause
{
    /**
     * @var CompletePattern[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add one or more patterns to the OPTIONAL MATCH clause.
     */
    public function addPattern(CompletePattern ...$pattern): self
    {
        $this->patterns = array_merge($this->patterns, $pattern);

        return $this;
    }

    /**
     * Returns the patterns to optionally match.
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
        return "OPTIONAL MATCH";
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
