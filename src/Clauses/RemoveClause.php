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

use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * This class represents a REMOVE clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 113)
 * @see Query::remove() for a more convenient method to construct this class
 */
final class RemoveClause extends Clause
{
    use ErrorTrait;

    /**
     * @var Label[]|Property[] the expressions in this REMOVE clause
     */
    private array $expressions = [];

    /**
     * Add one or more expressions to the REMOVE clause.
     *
     * @param Label|Property ...$expressions The expressions to add
     *
     * @return RemoveClause
     */
    public function addExpression(...$expressions): self
    {
        self::assertClassArray('expressions', [Property::class, Label::class], $expressions);
        $this->expressions = array_merge($this->expressions, $expressions);

        return $this;
    }

    /**
     * Returns the expressions in the REMOVE clause.
     *
     * @return Label[]|Property[]
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
        return "REMOVE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(static fn (QueryConvertible $expression) => $expression->toQuery(), $this->expressions)
        );
    }
}
