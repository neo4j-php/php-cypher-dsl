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

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a label. A label in Cypher would be something like "n:German" or "n:German:Swedish".
 */
class Label implements BooleanType
{
    use EscapeTrait;
    use BooleanTypeTrait;

    /**
     * @var Variable The variable to which this label belongs
     */
    private Variable $variable;

    /**
     * @var string[] The names of the labels
     */
    private array $labels;

    /**
     * Label constructor.
     *
     * @param Variable $variable
     * @param string[] $labels
     */
    public function __construct(Variable $variable, array $labels)
    {
        if (count($labels) === 0) {
            throw new InvalidArgumentException("\$labels must have at least one label");
        }

        foreach ($labels as $label) {
            if (!is_string($label)) {
                throw new InvalidArgumentException("\$labels must consist of only strings");
            }
        }

        $this->variable = $variable;
        $this->labels = $labels;
    }

    /**
     * Returns the labels.
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Returns the variable of the label.
     *
     * @return Variable
     */
    public function getVariable(): Variable
    {
        return $this->variable;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $query = $this->variable->toQuery();

        foreach ($this->labels as $label) {
            $query = sprintf("%s:%s", $query, $this->escape($label));
        }

        return $query;
    }
}
