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

use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents a RETURN clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 74)
 * @see Query::returning() for a more convenient method to construct this class
 */
final class ReturnClause extends Clause
{
    use CastTrait;
    use ErrorTrait;

    /**
     * @var bool Whether to be a RETURN DISTINCT query
     */
    private bool $distinct = false;

    /**
     * @var Alias[]|AnyType[]|(Alias|AnyType)[] The expressions to return
     */
    private array $columns = [];

    /**
     * Add a new column to this RETURN clause.
     *
     * @param Alias|AnyType|bool|float|int|mixed[]|Pattern|string ...$columns The values to return
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
     */
    public function addColumn(...$columns): self
    {
        $res = [];

        foreach ($columns as $column) {
            $res[] = $column instanceof Alias ? $column : self::toAnyType($column);
        }

        $this->columns = array_merge($this->columns, $res);

        return $this;
    }

    /**
     * Sets this query to only retrieve unique rows.
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-unique-results
     */
    public function setDistinct(bool $distinct = true): self
    {
        $this->distinct = $distinct;

        return $this;
    }

    /**
     * Returns the columns to return. Aliased columns have string keys instead of integers.
     *
     * @return Alias[]|AnyType[]|(Alias|AnyType)[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns whether the returned results are distinct.
     */
    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return $this->distinct ?
            "RETURN DISTINCT" :
            "RETURN";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(static fn (QueryConvertible $column) => $column->toQuery(), $this->columns)
        );
    }
}
