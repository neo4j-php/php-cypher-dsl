<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * This interface represents any pattern that can be related to another pattern using a relationship. These are:
 *
 * - node
 * - path
 */
interface RelatablePattern
{
    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using the given relationship
     * pattern.
     *
     * @param Relationship $relationship The relationship to use
     * @param RelatablePattern $relatable The relatable pattern to attach to this pattern
     *
     * @return Path
     */
    public function relationship(Relationship $relationship, RelatablePattern $relatable): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a right (-->)
     * relationship.
     *
     * @param RelatablePattern $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipTo(RelatablePattern $relatable, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a left (<--)
     * relationship.
     *
     * @param RelatablePattern $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipFrom(RelatablePattern $relatable, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a unidirectional
     * (--/<-->) relationship.
     *
     * @param RelatablePattern $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipUni(RelatablePattern $relatable, ?string $type = null, $properties = null, $name = null): Path;
}