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

use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This class represents a LIMIT clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 98)
 * @see Query::limit() for a more convenient method to construct this class
 */
final class LimitClause extends Clause
{
    use CastTrait;

    /**
     * The expression of the LIMIT statement.
     *
     * @var NumeralType|null $limit
     */
    private ?NumeralType $limit = null;

    /**
     * Sets the expression that returns the limit.
     *
     * TODO: Rewrite this to only accept IntegerType (this also requires work in other parts of the DSL)
     *
     * @param NumeralType|int $limit The limit
     * @return $this
     */
    public function setLimit($limit): self
    {
        $this->limit = self::toNumeralType($limit);

        return $this;
    }

    /**
     * Returns the limit of the clause.
     *
     * @return NumeralType|null
     */
    public function getLimit(): ?NumeralType
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "LIMIT";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (isset($this->limit)) {
            return $this->limit->toQuery();
        }

        return "";
    }
}
