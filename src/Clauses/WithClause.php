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

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;

/**
 * This class represents a WITH clause.
 *
 * The WITH clause allows query parts to be chained together, piping the results from one to be used as a starting point
 * or criteria in the next.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 78)
 * @see Query::with() for a more convenient method to construct this class
 */
final class WithClause extends Clause
{
    use CastTrait;

    /**
     * @var Alias[]|Variable[]|(Alias|Variable)[] The variables to include in the clause
     */
    private array $entries = [];

    /**
     * Add one or more new entries to the WITH clause.
     *
     * @param Alias|Pattern|string|Variable ...$entries The entries to add
     *
     * @return $this
     */
    public function addEntry(...$entries): self
    {
        $res = [];

        foreach ($entries as $entry) {
            $res[] = $entry instanceof Alias ? $entry : self::toVariable($entry);
        }

        $this->entries = array_merge($this->entries, $res);

        return $this;
    }

    /**
     * Returns the expression to include in the clause.
     *
     * @return Alias[]|Variable[]|(Alias|Variable)[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "WITH";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(static fn (QueryConvertible $expression) => $expression->toQuery(), $this->entries)
        );
    }
}
