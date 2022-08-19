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

use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a SET clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 107)
 * @see Query::set() for a more convenient method to construct this class
 */
final class SetClause extends Clause
{
    use ErrorTrait;

    /**
     * @var PropertyReplacement[]|Label[] $expressions The expressions to set
     */
    private array $expressions = [];

    /**
     * Add one or more expressions to this SET clause.
     *
     * @param PropertyReplacement|Label $expressions The expressions to add to this set clause
     * @return $this
     */
    public function add(...$expressions): self
    {
        $this->assertClassArray('expression', [PropertyReplacement::class, Label::class], $expressions);
        $this->expressions = array_merge($this->expressions, $expressions);

        return $this;
    }

    /**
     * Returns the expressions to SET.
     *
     * @return PropertyReplacement[]|Label[]
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "SET";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(fn (QueryConvertible $expression): string => $expression->toQuery(), $this->expressions)
        );
    }
}
