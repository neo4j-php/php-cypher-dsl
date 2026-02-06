<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents the EXISTS expression.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#filter-on-relationship-type Corresponding documentation on Neo4j.com
 */
final class Exists implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var MatchClause The MATCH part of the EXISTS expression
     */
    private MatchClause $match;

    /**
     * @var null|WhereClause The optional WHERE part of the EXISTS expression
     */
    private ?WhereClause $where;

    /**
     * @param MatchClause      $match The MATCH part of the EXISTS expression
     * @param null|WhereClause $where The optional WHERE part of the EXISTS expression
     *
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(MatchClause $match, ?WhereClause $where = null)
    {
        $this->match = $match;
        $this->where = $where;
    }

    /**
     * Returns the MATCH part of the EXISTS expression.
     */
    public function getMatch(): MatchClause
    {
        return $this->match;
    }

    /**
     * Returns the WHERE part of the expression.
     */
    public function getWhere(): ?WhereClause
    {
        return $this->where;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        if (isset($this->where)) {
            return sprintf(
                "EXISTS { %s %s }",
                $this->match->toQuery(),
                $this->where->toQuery()
            );
        }

        return sprintf("EXISTS { %s }", $this->match->toQuery());
    }
}
