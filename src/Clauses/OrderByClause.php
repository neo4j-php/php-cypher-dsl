<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Types\AnyType;
use function array_map;
use WikibaseSolutions\CypherDSL\Order;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This class represents an ORDER BY clause. This clause should always be preceded by a RETURN
 * or WITH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
 */
class OrderByClause extends Clause
{
    use EscapeTrait;

    /**
     * @var Order[] The expressions to include in the clause
     */
    private array $orderings = [];

    /**
     * @var bool
     */
    private bool $descending = false;

    /**
     * Add a property to sort on.
     *
     * @param PropertyType $property The additional property to sort on.
     * @param string|null $order The order of the property to appear. Null is equal to the default in Neo4J.
     *
     * @return OrderByClause
     */
    public function addProperty(PropertyType $property, ?string $order = null): self
    {
        $this->orderings[] = new Order($property, $order);

        return $this;
    }

    /**
     * Returns the properties to order.
     *
     * @return AnyType[]
     */
    public function getProperties(): array
    {
        return array_map(static fn (Order $o) => $o->getExpression(), $this->orderings);
    }

    /**
     * Returns the orderings.
     *
     * @return Order[]
     */
    public function getOrderings(): array
    {
        return $this->orderings;
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
        $properties = array_map(static fn ($x) => $x->toQuery(), $this->orderings);

        return implode(", ", $properties);
    }
}
