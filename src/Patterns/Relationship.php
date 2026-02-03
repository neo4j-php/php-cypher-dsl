<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

use DomainException;
use InvalidArgumentException;
use LogicException;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait;
use WikibaseSolutions\CypherDSL\Utils\NameUtils;

/**
 * This class represents an arbitrary relationship.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
 */
final class Relationship implements PropertyPattern
{
    use PropertyPatternTrait;

    public const DIR_RIGHT = ["-", "->"];

    public const DIR_LEFT = ["<-", "-"];

    public const DIR_UNI = ["-", "-"];

    /**
     * @var string[] The direction of the relationship (one of the DIR_* constants)
     */
    private array $direction;

    /**
     * @var string[] A list of relationship condition types
     */
    private array $types = [];

    /**
     * @var null|int The minimum number of `relationship->node` hops away to search
     */
    private ?int $minHops = null;

    /**
     * @var null|int The maximum number of `relationship->node` hops away to search
     */
    private ?int $maxHops = null;

    /**
     * @var null|int The exact number of `relationship->node` hops away to search
     */
    private ?int $exactHops = null;

    /**
     * @var bool Whether to allow arbitrary hops between nodes
     */
    private bool $arbitraryHops = false;

    /**
     * @param string[] $direction The direction of the relationship, should be either:
     *                            - Relationship::DIR_RIGHT (for a relation of (a)-->(b))
     *                            - Relationship::DIR_LEFT (for a relation of (a)<--(b))
     *                            - Relationship::DIR_UNI (for a relation of (a)--(b))
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(array $direction)
    {
        if ($direction !== self::DIR_RIGHT && $direction !== self::DIR_LEFT && $direction !== self::DIR_UNI) {
            throw new InvalidArgumentException("The direction must be either 'DIR_LEFT', 'DIR_RIGHT' or 'RELATED_TO'");
        }

        $this->direction = $direction;
    }

    /**
     * Set the minimum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels Corresponding documentation on Neo4j.com
     *
     * @return $this
     */
    public function withMinHops(int $minHops): self
    {
        if ($minHops < 0) {
            throw new DomainException("minHops cannot be less than 0");
        }

        if (isset($this->maxHops) && $minHops > $this->maxHops) {
            throw new DomainException("minHops cannot be greater than maxHops");
        }

        if (isset($this->exactHops) || $this->arbitraryHops) {
            throw new LogicException("minHops cannot be used in combination with exactHops or arbitraryHops");
        }

        $this->minHops = $minHops;

        return $this;
    }

    /**
     * Set the maximum number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels Corresponding documentation on Neo4j.com
     *
     * @return $this
     */
    public function withMaxHops(int $maxHops): self
    {
        if ($maxHops < 1) {
            throw new DomainException("maxHops cannot be less than 1");
        }

        if (isset($this->minHops) && $maxHops < $this->minHops) {
            throw new DomainException("maxHops cannot be less than minHops");
        }

        if (isset($this->exactHops) || $this->arbitraryHops) {
            throw new LogicException("maxHops cannot be used in combination with exactHops or arbitraryHops");
        }

        $this->maxHops = $maxHops;

        return $this;
    }

    /**
     * Set the exact number of `relationship->node` hops away to search.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/#varlength-rels Corresponding documentation on Neo4j.com
     *
     * @return $this
     */
    public function withExactHops(int $exactHops): self
    {
        if ($exactHops < 1) {
            throw new DomainException("exactHops cannot be less than 1");
        }

        if (isset($this->minHops) || isset($this->maxHops) || $this->arbitraryHops) {
            throw new LogicException("exactHops cannot be used in combination with minHops, maxHops or arbitraryHops");
        }

        $this->exactHops = $exactHops;

        return $this;
    }

    /**
     * Set the number of hops to be an arbitrary number (wildcard).
     *
     * @return $this
     */
    public function withArbitraryHops(bool $arbitraryHops = true): self
    {
        if (isset($this->minHops) || isset($this->maxHops) || isset($this->exactHops)) {
            throw new LogicException("arbitraryHops cannot be used in combination with minHops, maxHops or exactHops");
        }

        $this->arbitraryHops = $arbitraryHops;

        return $this;
    }

    /**
     * The types to require for this relationship. Will overwrite any previously set types.
     *
     * @param string[] $types
     *
     * @return $this
     */
    public function withTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    /**
     * Add one or more types to require for this relationship.
     *
     * @param string ...$type
     *
     * @return $this
     */
    public function addType(string ...$type): self
    {
        $this->types = array_merge($this->types, $type);

        return $this;
    }

    /**
     * Returns the direction of this relationship (one of the Relationship::DIR_* constants).
     *
     * @return string[]
     */
    public function getDirection(): array
    {
        return $this->direction;
    }

    /**
     * Returns the exact amount of hops configured.
     */
    public function getExactHops(): ?int
    {
        return $this->exactHops;
    }

    /**
     * Returns the maximum amount of hops configured.
     */
    public function getMaxHops(): ?int
    {
        return $this->maxHops;
    }

    /**
     * Returns the minimum amount of hops configured.
     */
    public function getMinHops(): ?int
    {
        return $this->minHops;
    }

    /**
     * Returns the types of the relationship.
     *
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     */
    public function toQuery(): string
    {
        return $this->direction[0] . $this->relationshipDetailToString() . $this->direction[1];
    }

    /**
     * Converts the relationship details (the inner part of the relationship) to Cypher syntax.
     */
    private function relationshipDetailToString(): string
    {
        if (isset($this->variable)) {
            $conditionInner = $this->variable->toQuery();
        } else {
            $conditionInner = "";
        }

        $types = array_filter($this->types);

        if (count($types) !== 0) {
            // If we have at least one condition type, escape them and insert them into the query
            $escapedTypes = array_map(static fn (string $type): string => NameUtils::escape($type), $types);
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
            // We have an exact number of hops
            $conditionInner .= '*' . $this->exactHops;
        } elseif ($this->arbitraryHops) {
            // We have an arbitrary number of hops
            $conditionInner .= '*';
        }

        if (isset($this->properties)) {
            // Only add the properties if they're not empty
            if (!$this->properties instanceof Map || $this->properties->getElements() !== []) {
                $map = $this->properties->toQuery();

                if ($conditionInner !== "") {
                    // Add some padding between the property map and the preceding structure
                    $conditionInner .= " ";
                }

                $conditionInner .= $map;
            }
        }

        if ($conditionInner === '') {
            // If there is no condition, we can also omit the square brackets
            return '';
        }

        return sprintf("[%s]", $conditionInner);
    }
}
