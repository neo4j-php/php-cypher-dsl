<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\PatternTraits;

use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This trait provides a default implementation to satisfy the "PropertyPattern" interface.
 */
trait PropertyPatternTrait
{
    use CastTrait;
    use PatternTrait;

    /**
     * @var null|MapType The properties of this object
     */
    private ?MapType $properties = null;

    /**
     * @inheritDoc
     */
    public function property(string $property): Property
    {
        return new Property($this->getVariable(), $property);
    }

    /**
     * @inheritDoc
     */
    public function withProperties($properties): self
    {
        $this->properties = self::toMapType($properties);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProperty(string $key, $property): self
    {
        $this->makeMap()->add($key, $property);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProperties(Map|array $properties): self
    {
        $map = $this->makeMap();

        if (is_array($properties)) {
            $res = array_map(function ($property) {
                return self::toAnyType($property);
            }, $properties);

            // Cast the array to a Map
            $properties = new Map($res);
        }

        $map->mergeWith($properties);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProperties(): ?MapType
    {
        return $this->properties;
    }

    /**
     * Initialises the properties in this pattern.
     */
    private function makeMap(): Map
    {
        if (!isset($this->properties)) {
            $this->properties = new Map();
        } elseif (!$this->properties instanceof Map) {
            // Adding to a map is not natively supported by the MapType, but it is supported by Map. Syntactically, it
            // is not possible to add new items to, for instance, a Variable, even though it implements MapType. It is
            // however still useful to be able to add items to objects where a Map is used (that is, an object of
            // MapType with the {} syntax).
            throw new TypeError('$this->properties must be of type Map to support "addProperty"');
        }

        return $this->properties;
    }
}
