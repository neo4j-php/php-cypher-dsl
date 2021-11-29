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

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\AndOperator;
use WikibaseSolutions\CypherDSL\OrOperator;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\XorOperator;

/**
 * This trait should be used by any expression that returns a boolean.
 */
trait BooleanTypeTrait
{
    use PropertyTypeTrait;

    /**
     * Create a conjunction between this expression and the given expression.
     *
     * @param BooleanType $right
     * @return AndOperator
     */
    public function and(BooleanType $right): AndOperator
    {
        return new AndOperator($this, $right);
    }

    /**
     * Create a disjunction between this expression and the given expression.
     *
     * @param BooleanType $right
     * @return OrOperator
     */
    public function or(BooleanType $right): OrOperator
    {
        return new OrOperator($this, $right);
    }

    /**
     * Perform an XOR with the given expression.
     *
     * @param BooleanType $right
     * @return XorOperator
     */
    public function xor(BooleanType $right): XorOperator
    {
        return new XorOperator($this, $right);
    }
}