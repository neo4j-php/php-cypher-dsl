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

use function is_string;
use function trim;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Variable;

trait HasVariableTrait
{
    use ErrorTrait;

    /**
     * @var Variable|null
     */
    private ?Variable $variable = null;

    /**
     * Names the node with a variable. If the variable is an empty string or null, it will be unset.
     *
     * @param Variable|string|null $variable
     *
     * @return static
     */
    public function named($variable): self
    {
        self::assertClass('variable', ['string', 'null', Variable::class], $variable);

        if (is_string($variable)) {
            if (trim($variable) === '') {
                $variable = null;
            } else {
                $variable = new Variable($variable);
            }
        }


        $this->variable = $variable;

        return $this;
    }

    /**
     * Alias of Node::named().
     *
     * @param $variable
     * @return $this
     * @see Node::named()
     */
    public function setName($variable): self
    {
        return $this->named($variable);
    }

    /**
     * Returns the variable of the path.
     *
     * @return Variable|null
     */
    public function getVariable(): ?Variable
    {
        return $this->variable;
    }

    /**
     * Returns the name of this Structural Type. This function automatically generates a name if the node does not have a
     * name yet.
     *
     * @return Variable The name of this node
     */
    public function getName(): Variable
    {
        if (!isset($this->variable)) {
            $this->named(new Variable());
        }

        return $this->variable;
    }
}
