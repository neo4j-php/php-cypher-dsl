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
use WikibaseSolutions\CypherDSL\Query;

/**
 * This class represents a CREATE clause.
 *
 * The CREATE clause is used to create graph elements — nodes and relationships.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 99)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/
 * @see Query::create() for a more convenient method to construct this class
 */
final class CreateClause extends Clause
{
    /**
     * @var CompletePattern[] The patterns to create
     */
    private array $patterns = [];

    /**
     * Add one or more patterns to create.
     *
     * @param CompletePattern ...$pattern The patterns to add to the CREATE clause
     *
     * @return $this
     */
    public function addPattern(CompletePattern ...$pattern): self
    {
        $this->patterns = array_merge($this->patterns, $pattern);

        return $this;
    }

    /**
     * Returns the patterns of the CREATE clause.
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
        return "CREATE";
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
