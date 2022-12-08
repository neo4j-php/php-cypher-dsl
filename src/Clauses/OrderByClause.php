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

use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Query;

/**
 * This class represents an ORDER BY sub-clause.
 *
 * ORDER BY is a sub-clause following RETURN or WITH, and it specifies that the output should be sorted
 * and how.
 *
 * TODO: Allow order modifier to be applied for each property (see #39).
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 93)
 * @see Query::orderBy() for a more convenient method to construct this class
 */
final class OrderByClause extends Clause
{
    /**
     * @var Property[] The expressions to include in the clause
     */
    private array $properties = [];

    /**
     * @var bool Whether to add the DESC modifier to the clause
     */
    private bool $descending = false;

    /**
     * Add one or more properties to sort on.
     *
     * @param Property ...$property The additional property to sort on
     *
     * @return OrderByClause
     */
    public function addProperty(Property ...$property): self
    {
        $this->properties = array_merge($this->properties, $property);

        return $this;
    }

    /**
     * Set to sort in a DESCENDING order.
     *
     * @return OrderByClause
     */
    public function setDescending(bool $descending = true): self
    {
        $this->descending = $descending;

        return $this;
    }

    /**
     * Returns the properties to order.
     *
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * Returns whether the ordering is in descending order.
     */
    public function isDescending(): bool
    {
        return $this->descending;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "ORDER BY";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        $properties = array_map(static fn (Property $property): string => $property->toQuery(), $this->properties);
        $subject = implode(", ", $properties);

        if ($this->descending) {
            $subject .= ' DESCENDING';
        }

        return $subject;
    }
}
