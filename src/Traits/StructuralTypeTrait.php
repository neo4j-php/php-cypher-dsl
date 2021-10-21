<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This trait should be used by any expression that returns a structural type.
 *
 * @note This trait should not be used by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
trait StructuralTypeTrait
{
    /**
     * Creates a new relationship from this node to the given pattern.
     *
     * @param  StructuralType $pattern
     * @return Path
     */
    public function relationshipTo(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_RIGHT);
    }

    /**
     * Creates a new relationship from the given pattern to this node.
     *
     * @param  StructuralType $pattern
     * @return Path
     */
    public function relationshipFrom(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_LEFT);
    }

    /**
     * Creates a new unidirectional relationship between this node and the given pattern.
     *
     * @param  StructuralType $pattern
     * @return Path
     */
    public function relationshipUni(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_UNI);
    }
}