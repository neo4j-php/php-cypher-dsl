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

/**
 * This class represents a SET clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
 */
class SetClause extends Clause
{
    /**
     * @var AnyType[] $expressions The expressions to set
     */
    private array $expressions = [];

    /**
     * Add an expression to set. This expression is usually an "assignment" expression.
     *
     * @param  AnyType $expression The expression to set
     * @return SetClause
     */
    public function addExpression(AnyType $expression): self
    {
        $this->expressions[] = $expression;

        return $this;
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
            array_map(fn (AnyType $expression): string => $expression->toQuery(), $this->expressions)
        );
    }
}