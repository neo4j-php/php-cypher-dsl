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

namespace WikibaseSolutions\CypherDSL;

use function in_array;
use InvalidArgumentException;
use function strtoupper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Defines the order of an expression. Can only be used in an ORDER BY clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
 * @note While the documentation online does not mention this, ORDER BY supports multiple directions in the same clause:
 *      - ORDER BY a ASC, b DESC
 *      is considered valid.
 *      This means it is impossible for the OrderBy clause to order all expressions individually, necessitating this class.
 */
class Order implements QueryConvertable
{
    private PropertyType $expression;
    /** @var string|null */
    private ?string $ordering;

    /**
     * Order constructor.
     *
     * @param PropertyType $expression The expression to order by.
     * @param string|null $ordering The order modifier. Must be null or a valid modifier ('ASC', 'ASCENDING', 'DESC', 'DESCENDING')
     */
    public function __construct(PropertyType $expression, ?string $ordering = null)
    {
        $this->expression = $expression;
        $this->setOrdering($ordering);
    }

    /**
     * Returns the expression being ordered.
     *
     * @return AnyType
     */
    public function getExpression(): AnyType
    {
        return $this->expression;
    }

    /**
     * @return string|null
     */
    public function getOrdering(): ?string
    {
        return $this->ordering;
    }

    public function setOrdering(?string $ordering): self
    {
        if ($ordering !== null) {
            $ordering = strtoupper($ordering);
            if (!in_array($ordering, ['ASC', 'DESC', 'ASCENDING', 'DESCENDING'])) {
                throw new InvalidArgumentException('Ordering must be null, "ASC", "DESC", "ASCENDING" or "DESCENDING"');
            }

            $this->ordering = $ordering;
        } else {
            $this->ordering = null;
        }

        return $this;
    }

    public function toQuery(): string
    {
        $cypher = $this->getExpression()->toQuery();
        if ($this->ordering) {
            $cypher .= ' ' . $this->ordering;
        }

        return $cypher;
    }
}
