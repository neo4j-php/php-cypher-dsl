<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\Inequality;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This trait should be used by any expression that returns a property type.
 *
 * @note This trait should not be used by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#property-types
 */
trait PropertyTypeTrait
{
    /**
     * Perform an equality check or an assignment with the given expression.
     *
     * @param  PropertyType $right
     * @return Equality
     */
    public function equals(PropertyType $right): Equality
    {
        return new Equality($this, $right);
    }

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param  PropertyType $right
     * @return Inequality
     */
    public function notEquals(PropertyType $right): Inequality
    {
        return new Inequality($this, $right);
    }
}