<?php

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

trait PropertiesTrait
{
	use ErrorTrait;

	/**
	 * @var PropertyMap|null
	 */
	private ?PropertyMap $properties = null;

	/**
	 * Add the given property to the properties of this object.
	 *
	 * @param string $key The name of the property
	 * @param PropertyType|string|bool|float|int $value The value of the property
	 *
	 * @return $this
	 */
	public function addProperty(string $key, $value): self
	{
		if ($this->properties === null) {
			$this->properties = new PropertyMap();
		}

		$this->properties->addProperty($key, $value);

		return $this;
	}

	/**
	 * Add the given properties to the properties of this object. This function automatically converts any native type
	 * into a Cypher literal.
	 *
	 * @param PropertyMap|PropertyType[]|string[]|bool[]|float[]|int[] $properties
	 *
	 * @return $this
	 */
	public function addProperties($properties): self
	{
		self::assertClass('properties', [PropertyMap::class, 'array'], $properties);

		if ($properties instanceof PropertyMap) {
			$this->properties->mergeWith($properties);
		} else {
			foreach ($properties as $key => $value) {
				$this->addProperty($key, $value);
			}
		}

		return $this;
	}
}