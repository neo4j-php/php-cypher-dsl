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

use Stringable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * Represents patterns to which properties can be assigned. These are:.
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
     */
    public function property(string $property): Property;

    /**
     * Set the properties of this pattern.
     *
     * @return $this
     */
    public function withProperties(MapType|array $properties): self;

    /**
     * Add a property to the properties in this pattern. This is only possible if the properties in this pattern are
     * a map. An exception will be thrown if they are anything else (such as a variable). If the pattern  does not yet
     * contain any properties, a new map will be created.
     *
     * @return $this
     */
    public function addProperty(string $key, AnyType|Pattern|Stringable|bool|float|int|array|string $property): self;

    /**
     * Add the given properties to this pattern. This is only possible if the properties in this pattern are a map.
     * An exception will be thrown if they are anything else (such as a variable). If the pattern does not yet contain
     * any properties, a new map will be created.
     *
     * @return $this
     */
    public function addProperties(Map|array $properties): self;

    /**
     * Returns the properties of this object.
     */
    public function getProperties(): ?MapType;
}
