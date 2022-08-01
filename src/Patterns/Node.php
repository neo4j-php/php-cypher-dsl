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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\PatternPropertiesTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This class represents a node.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 8)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
 */
class Node extends Pattern implements Relatable
{
    use ErrorTrait;
    use EscapeTrait;
    use PatternPropertiesTrait;

    /**
     * @var string[] The labels of this node
     */
    private array $labels = [];

    /**
     * @var MapType|null The properties of this relationship
     */
    private ?MapType $properties = null;

    /**
     * Node constructor.
     *
     * @param string|null $label
     */
    public function __construct(string $label = null)
    {
        if ($label !== null) {
            $this->labels[] = $label;
        }
    }

    /**
     * Sets the labels of this node. This overwrites any previously set labels.
     *
     * @param string[] $labels
     * @return $this
     */
    public function withLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Adds a label to the node.
     *
     * @param string $label
     * @return $this
     */
    public function addLabel(string $label): self
    {
        $this->labels[] = $label;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function relationship(Relationship $relationship, Relatable $relatable): Path
    {
        return (new Path($this))->relationship($relationship, $relatable);
    }

    /**
     * @inheritDoc
     */
    public function relationshipTo(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipTo($relatable, $type, $properties, $name);
    }

    /**
     * @inheritDoc
     */
    public function relationshipFrom(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipFrom($relatable, $type, $properties, $name);
    }

    /**
     * @inheritDoc
     */
    public function relationshipUni(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipUni($relatable, $type, $properties, $name);
    }

    /**
     * Returns the labels of the node.
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Returns the properties of this node.
     *
     * @return MapType|null
     */
    public function getProperties(): ?MapType
    {
        return $this->properties;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        return sprintf("(%s)", $this->nodeInnerToString());
    }

    /**
     * Returns the string representation of the inner part of a node.
     *
     * @return string
     */
    private function nodeInnerToString(): string
    {
        $nodeInner = "";

        if (isset($this->variable)) {
            $nodeInner .= $this->variable->toQuery();
        }

        if ($this->labels !== []) {
            foreach ($this->labels as $label) {
                $nodeInner .= ":{$this->escape($label)}";
            }
        }

        if (isset($this->properties)) {
            if ($nodeInner !== "") {
                $nodeInner .= " ";
            }

            $nodeInner .= $this->properties->toQuery();
        }

        return $nodeInner;
    }
}
