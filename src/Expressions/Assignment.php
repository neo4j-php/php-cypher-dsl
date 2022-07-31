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

namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents the application of the assignment (=/+=) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/#set-set-a-property
 * @see Equality For a semantically different, but syntactically identical operator
 */
class Assignment extends BinaryOperator
{
    use ErrorTrait;

    /**
     * @var bool Whether to use the property mutation instead of the property replacement
     * operator.
     */
    private bool $mutate = false;

    /**
     * @param Property|Variable $left
     * @inheritDoc
     */
    public function __construct(AnyType $left, AnyType $right)
    {
        self::assertClass('left', [Property::class, Variable::class], $left);

        parent::__construct($left, $right, false);
    }

    /**
     * Whether to use the property mutation instead of the property replacement
     * operator.
     *
     * @param bool $mutate
     * @return $this
     */
    public function setMutate(bool $mutate = true): self
    {
        $this->mutate = $mutate;

        return $this;
    }

    /**
     * Returns whether the assignment uses property mutation instead of replacement.
     *
     * @return bool
     */
    public function mutates(): bool
    {
        return $this->mutate;
    }

    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return $this->mutate ? "+=" : "=";
    }
}
