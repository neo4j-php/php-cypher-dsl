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

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\RelatablePatternTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * Represents patterns that can be related to one another using a relationship. These are:
 *
 * - node
 * - path
 *
 * @see RelatablePatternTrait for a default implementation
 */
interface RelatablePattern extends Pattern
{
    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using the given relationship
     * pattern.
     *
     * @param Relationship $relationship The relationship to use
     * @param RelatablePattern $pattern The relatable pattern to attach to this pattern
     *
     * @return Path
     */
    public function relationship(Relationship $relationship, self $pattern): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a right (-->)
     * relationship.
     *
     * @param RelatablePattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipTo(self $pattern, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a left (<--)
     * relationship.
     *
     * @param RelatablePattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipFrom(self $pattern, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a unidirectional
     * (--/<-->) relationship.
     *
     * @param RelatablePattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipUni(self $pattern, ?string $type = null, $properties = null, $name = null): Path;
}
