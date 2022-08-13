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

/**
 * Represents a raw clause.
 *
 * A raw clause is not a real clause, but rather a construct for php-cypher-dsl to support unimplemented
 * clauses.
 */
final class RawClause extends Clause
{
    /**
     * @var string The name of the clause; for instance "MATCH"
     */
    private string $clause;

    /**
     * @var string The subject/body of the clause
     */
    private string $subject;

    /**
     * RawClause constructor.
     *
     * @param string $clause The name of the clause; for instance "MATCH"
     * @param string $subject The subject/body of the clause
     */
    public function __construct(string $clause = "", string $subject = "")
    {
        $this->clause = $clause;
        $this->subject = $subject;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return $this->clause;
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return $this->subject;
    }
}
