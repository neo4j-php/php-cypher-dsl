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

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\PathTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * This class represents an arbitrary relationship between two nodes, a node and a
 * relationship or between two relationships.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
 */
class Path implements PathType
{
    use EscapeTrait;
    use PathTypeTrait;

    const DIR_RIGHT = ["-", "->"];
    const DIR_LEFT = ["<-", "-"];
    const DIR_UNI = ["-", "-"];

    /**
     * @var StructuralType The pattern left of the relationship
     */
    private AnyType $a;

    /**
     * @var StructuralType The pattern right of the relationship
     */
    private AnyType $b;

    /**
     * @var string[] The direction of the relationship
     */
    private array $direction;

    /**
     * @var string[]
     */
    private array $types = [];

    /**
     * @var Variable
     */
    private Variable $variable;

    /**
     * @var int The minimum number of `relationship->node` hops away to search
     */
    private int $minHops;

    /**
     * @var int The maximum number of `relationship->node` hops away to search
     */
    private int $maxHops;

    /**
     * Path constructor.
     *
     * @param StructuralType $a The node left of the relationship
     * @param StructuralType $b The node right of the relationship
     * @param array $direction The direction of the relationship, should be either:
     *                           - Path::DIR_RIGHT (for a relation of
     *                           (a)-->(b)) - Path::DIR_LEFT (for a relation
     *                           of (a)<--(b)) - Path::DIR_UNI (for a
     *                           relation of (a)--(b))
     */
    public function __construct(StructuralType $a, StructuralType $b, array $direction)
    {
        $this->a = $a;
        $this->b = $b;

        if ($direction !== self::DIR_RIGHT && $direction !== self::DIR_LEFT && $direction !== self::DIR_UNI) {
            throw new InvalidArgumentException("The direction must be either 'DIR_LEFT', 'DIR_RIGHT' or 'RELATED_TO'");
        }

        $this->direction = $direction;
    }

    /**
     * @param Variable|string $variable
     * @return Path
     */
    public function named($variable): self
    {
        if (!($variable instanceof Variable)) {
            $variable = new Variable($variable);
        }

        $this->variable = $variable;

        return $this;
    }

    /**
     * Set the minimum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $minHops
     * @return Path
     */
    public function withMinHops(int $minHops): self
    {
        $this->minHops = $minHops;

        return $this;
    }

    /**
     * Set the maximum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $maxHops
     * @return Path
     */
    public function withMaxHops(int $maxHops): self
    {
        $this->maxHops = $maxHops;

        return $this;
    }

    /**
     * Set the exact number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels
     *
     * @param int $exactHops
     * @return Path
     */
    public function withExactHops(int $exactHops) : self
    {
        $this->exactHops = $exactHops;

        return $this;
    }

    /**
     * @param string $type
     * @return Path
     */
    public function withType(string $type): self
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        $a = $this->a->toQuery();
        $b = $this->b->toQuery();

        return $a . $this->direction[0] . $this->conditionToString() . $this->direction[1] . $b;
    }

    /**
     * @return string
     */
    private function conditionToString(): string
    {
        $conditionInner = "";

        // The condition always starts with the variable
        if (isset($this->variable)) {
            $conditionInner .= $this->variable->toQuery();
        }

        $types = array_filter($this->types);

        if (count($types) !== 0) {
            // If we have at least one condition type, escape them and insert them into the query
            $escapedTypes = array_map(fn (string $type): string => $this->escape($type), $types);
            $conditionInner .= sprintf(":%s", implode("|", $escapedTypes));
        }

        if (isset($this->minHops) || isset($this->maxHops)) {
            // We have either a minHop, maxHop or both
            $conditionInner .= "*";

            if (isset($this->minHops)) {
                $conditionInner .= $this->minHops;
            }

            $conditionInner .= '..';

            if (isset($this->maxHops)) {
                $conditionInner .= $this->maxHops;
            }
        } elseif (isset($this->exactHops)) {
            $conditionInner .= '*' . $this->exactHops;
        }

        if (isset($this->properties)) {
            if ($conditionInner !== "") {
                // Add some padding between the property list and the preceding structure
                $conditionInner .= " ";
            }

            $conditionInner .= $this->properties->toQuery();
        }

        return sprintf("[%s]", $conditionInner);
    }
}
