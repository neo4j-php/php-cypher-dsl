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

use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This class represents a LIMIT clause.
 *
 * LIMIT constrains the number of records in the output.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 98)
 * @see Query::limit() for a more convenient method to construct this class
 */
final class LimitClause extends Clause
{
    /**
     * The expression of the LIMIT statement.
     */
    private ?IntegerType $limit = null;

    /**
     * Sets the expression that returns the limit.
     */
    public function setLimit(IntegerType|int $limit): self
    {
        $this->limit = CastUtils::toIntegerType($limit);

        return $this;
    }

    /**
     * Returns the limit of the clause.
     */
    public function getLimit(): ?IntegerType
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
