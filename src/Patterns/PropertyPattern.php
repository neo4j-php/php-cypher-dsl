<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * Represents patterns to which properties can be assigned. These are:
 *
 * - node
 * - relationship
 *
 * @see PropertyPatternTrait for a default implementation
 */
interface PropertyPattern extends Pattern
{
    /**
     * Returns the property of the given name in this pattern.
     *
     * @param string $property
     * @return Property
     */
    public function property(string $property): Property;

    /**
     * Set the properties of this pattern.
     *
     * @param MapType|array $properties
     * @return $this
     */
    public function withProperties($properties): self;

    /**
     * Add a property to the properties in this pattern. This is only possible if the properties in this pattern are
     * a map. An exception will be thrown if they are anything else (such as a variable). If the pattern  does not yet
     * contain any properties, a new map will be created.
     *
     * @param string $key
     * @param mixed $property
     * @return $this
     */
    public function addProperty(string $key, $property): self;

    /**
     * Add the given properties to this pattern. This is only possible if the properties in this pattern are a map.
     * An exception will be thrown if they are anything else (such as a variable). If the pattern  does not yet contain
     * any properties, a new map will be created.
     *
     * @param Map|array $properties
     * @return $this
     */
    public function addProperties($properties): self;

    /**
     * Returns the properties of this object.
     *
     * @return MapType|null
     */
    public function getProperties(): ?MapType;
}
