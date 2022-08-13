<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * Represents the EXISTS expression.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#filter-on-relationship-type
 */
final class Exists implements BooleanType
{
    use ErrorTrait;
    use BooleanTypeTrait;

    /**
     * @var MatchClause The MATCH part of the EXISTS expression
     */
    private MatchClause $match;

    /**
     * @var WhereClause|null The optional WHERE part of the EXISTS expression
     */
    private ?WhereClause $where;

    /**
     * @var bool Whether to insert parentheses around the expression
     */
    private bool $insertParentheses;

    /**
     * @param MatchClause $match The MATCH part of the EXISTS expression
     * @param WhereClause|null $where The optional WHERE part of the EXISTS expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(MatchClause $match, WhereClause $where = null, bool $insertParentheses = false)
    {
        $this->match = $match;
        $this->where = $where;
        $this->insertParentheses = $insertParentheses;
    }

    /**
     * Returns the MATCH part of the EXISTS expression.
     *
     * @return MatchClause
     */
    public function getMatch(): MatchClause
    {
        return $this->match;
    }

    /**
     * Returns the WHERE part of the expression.
     *
     * @return WhereClause|null
     */
    public function getWhere(): ?WhereClause
    {
        return $this->where;
    }

    /**
     * Returns whether it inserts parentheses around the expression.
     *
     * @return bool
     */
    public function insertsParentheses(): bool
    {
        return $this->insertParentheses;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        if (isset($this->where)) {
            return sprintf(
                $this->insertParentheses ? "(EXISTS { %s %s })" : "EXISTS { %s %s }",
                $this->match->toQuery(),
                $this->where->toQuery()
            );
        }

        return sprintf($this->insertParentheses ? "(EXISTS { %s })" : "EXISTS { %s }", $this->match->toQuery());
    }
}
