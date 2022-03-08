<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use function is_array;

trait HasPropertiesTrait
{
    use ErrorTrait;
    use MapTypeTrait;
    use StructuralTypeTrait;

    private PropertyMap $properties;

    /**
     * Add the given property to the properties of this node.
     *
     * @param string $key The name of the property
     * @param AnyType $value The value of the property
     *
     * @return static
     */
    public function withProperty(string $key, AnyType $value): self
    {
        $this->properties->addProperty($key, $value);

        return $this;
    }

    /**
     * Add the given properties to the properties of this node.
     *
     * @param PropertyMap|array $properties
     *
     * @return static
     */
    public function withProperties($properties): self
    {
        $this->assertClassOrType('properties', [PropertyMap::class, 'array'], $properties);

        $properties = is_array($properties) ? new PropertyMap($properties) : $properties;

        $this->properties->mergeWith($properties);

        return $this;
    }

    /**
     * Returns the property of the given name for this node. For instance, if this node is "(foo:PERSON)", a function call
     * like $node->property("bar") would yield "foo.bar".
     *
     * @param string $property
     * @return Property
     */
    public function property(string $property): Property
    {
        return new Property($this->getName(), $property);
    }
}