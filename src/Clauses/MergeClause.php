<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a MERGE clause.
 *
 * The MERGE clause ensures that a pattern exists in the graph. Either the pattern already exists, or it
 * needs to be created.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 115)
 */
final class MergeClause extends Clause
{
    /**
     * @var CompletePattern|null $pattern The pattern to merge
     */
    private ?CompletePattern $pattern = null;

    /**
     * @var SetClause|null $createClause The clause to execute when the pattern is created
     */
    private ?SetClause $createClause = null;

    /**
     * @var SetClause|null $matchClause The clause to execute when the pattern is matched
     */
    private ?SetClause $matchClause = null;

    /**
     * Sets the pattern to merge.
     *
     * @param CompletePattern $pattern The pattern to merge
     * @return $this
     */
    public function setPattern(CompletePattern $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * The clause to execute on all nodes that need to be created.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/#merge-merge-with-on-create
     *
     * @param SetClause|null $createClause
     * @return $this
     */
    public function setOnCreate(?SetClause $createClause): self
    {
        $this->createClause = $createClause;

        return $this;
    }

    /**
     * The clause to execute on all found nodes.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/#merge-merge-with-on-match
     *
     * @param SetClause|null $matchClause
     * @return $this
     */
    public function setOnMatch(?SetClause $matchClause): self
    {
        $this->matchClause = $matchClause;

        return $this;
    }

    /**
     * Returns the pattern to MERGE.
     *
     * @return CompletePattern|null
     */
    public function getPattern(): ?CompletePattern
    {
        return $this->pattern;
    }

    /**
     * Returns the clause to execute when the pattern is matched.
     *
     * @return SetClause|null
     */
    public function getOnCreateClause(): ?SetClause
    {
        return $this->createClause;
    }

    /**
     * Returns the clause to execute when the pattern is matched.
     *
     * @return SetClause|null
     */
    public function getOnMatchClause(): ?SetClause
    {
        return $this->matchClause;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "MERGE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->pattern)) {
            return "";
        }

        $queryParts = [$this->pattern->toQuery()];

        if (isset($this->createClause)) {
            $queryParts[] = sprintf("ON CREATE %s", $this->createClause->toQuery());
        }

        if (isset($this->matchClause)) {
            $queryParts[] = sprintf("ON MATCH %s", $this->matchClause->toQuery());
        }

        return implode(" ", $queryParts);
    }
}
