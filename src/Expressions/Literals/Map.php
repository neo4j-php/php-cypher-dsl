<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This class represents a CYPHER map. For example, this class can represent the following
 * construct:.
 *
 * {name: 'Andy', sport: 'Brazilian Ju-Jitsu'}
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/maps/ Corresponding documentation on Neo4j.com
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-properties Corresponding documentation on Neo4j.com
 */
final class Map implements MapType
{
    use CastTrait;
    use EscapeTrait;
    use MapTypeTrait;

    /**
     * @var AnyType[] The map
     */
    private array $elements;

    /**
     * @param mixed[] $elements Associative array of the elements that this map should have
     *
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(array $elements = [])
    {
        $this->elements = array_map([self::class, 'toAnyType'], $elements);
    }

    /**
     * Adds an element for the given name with the given value. Overrides the element if the $key already exists.
     *
     * @param string $key   The name/label for the element
     * @param mixed  $value The value of the element
     *
     * @return $this
     */
    public function add(string $key, $value): self
    {
        $this->elements[$key] = self::toAnyType($value);

        return $this;
    }

    /**
     * Merges the given map with this map.
     *
     * @param Map $map The map to merge
     *
     * @return $this
     */
    public function mergeWith(self $map): self
    {
        $this->elements = array_merge($this->elements, $map->getElements());

        return $this;
    }

    /**
     * Returns the elements of this map as an associative array with key-value pairs.
     *
     * @return AnyType[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Checks if this map is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $pairs = [];

        foreach ($this->elements as $key => $value) {
            $pairs[] = sprintf("%s: %s", self::escape((string) $key), $value->toQuery());
        }

        return sprintf("{%s}", implode(", ", $pairs));
    }
}
