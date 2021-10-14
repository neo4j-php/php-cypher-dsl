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

namespace WikibaseSolutions\CypherDSL\Expressions\Types;

use WikibaseSolutions\CypherDSL\Expressions\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Division;
use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * This trait should be used by any function that returns a numeral.
 */
trait NumeralTypeTrait
{
    /**
     * Add this expression to the given expression.
     *
     * @param  NumeralType $right
     * @return Addition
     */
    public function plus(NumeralType $right): Addition
    {
        return new Addition($this, $right);
    }

    /**
     * Divide this expression by the given expression.
     *
     * @param  NumeralType $right
     * @return Division
     */
    public function divide(NumeralType $right): Division
    {
        return new Division($this, $right);
    }
}