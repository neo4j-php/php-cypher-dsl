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

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Label;
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * This class represents a REMOVE clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/
 */
class RemoveClause extends Clause
{
    use ErrorTrait;

    /**
     * @var Property[]|Label[] The expressions in this REMOVE clause.
     */
    private array $expressions = [];

    /**
     * Add an expression to the REMOVE clause. This expression usually returns a property (a.b) or a label (a:b).
     *
     * @param Property|Label $expression The expression to add
     * @return RemoveClause
     */
    public function addExpression($expression): self
    {
        $this->assertClass('expression', [Property::class, Label::class], $expression);

        $this->expressions[] = $expression;

        return $this;
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
            array_map(fn (QueryConvertable $expression) => $expression->toQuery(), $this->expressions)
        );
    }
}
