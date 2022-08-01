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

use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a label. A label in Cypher would be something like "n:German" or "n:German:Swedish". Label implements
 * BooleanType, since it can be used in a "WHERE" clause like so:
 *
 *  MATCH (n) WHERE n:label1:label2 RETURN n
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 85)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#filter-on-node-label
 */
class Label implements BooleanType
{
    use BooleanTypeTrait;
    use EscapeTrait;

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
     * @param string ...$labels
     */
    public function __construct(Variable $variable, string ...$labels)
    {
        $this->variable = $variable;
        $this->labels = $labels;
    }

    /**
     * Overrides the labels of this class with the given labels.
     *
     * @param string ...$labels
     * @return $this
     */
    public function withLabels(string ...$labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Adds the given labels to this class.
     *
     * @param string ...$labels
     * @return $this
     */
    public function addLabels(string ...$labels): self
    {
        $this->labels = array_merge($this->labels, $labels);

        return $this;
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
