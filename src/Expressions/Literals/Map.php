<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This class represents a map of properties. For example, this class can represent the following
 * construct:
 *
 * {name: 'Andy', sport: 'Brazilian Ju-Jitsu'}
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-properties
 */
final class Map implements MapType
{
    use EscapeTrait;
    use ErrorTrait;
    use MapTypeTrait;

    /**
     * @var AnyType[] The map of properties
     */
    private array $properties;

    /**
     * @param AnyType[] $properties The map of expression
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(array $properties = [])
    {
        self::assertClassArray('properties', AnyType::class, $properties);
        $this->properties = $properties;
    }

    /**
     * Adds a property for the given name with the given value. Overrides the property if it already exists.
     *
     * @param string $key The name of the property
     * @param mixed $value The value of the property
     * @return $this
     */
    public function addProperty(string $key, $value): self
    {
        if (!$value instanceof AnyType) {
            $value = Literal::literal($value);
        }

        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Merges the given map with this map.
     *
     * @param Map $map The map to merge
     * @return $this
     */
    public function mergeWith(Map $map): self
    {
        $this->properties = array_merge($this->properties, $map->properties);

        return $this;
    }

    /**
     * Returns the map of properties as a number of key-expression pairs.
     *
     * @return AnyType[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $pairs = [];

        foreach ($this->properties as $key => $value) {
            $pairs[] = sprintf("%s: %s", $this->escape(strval($key)), $value->toQuery());
        }

        return sprintf("{%s}", implode(", ", $pairs));
    }
}
