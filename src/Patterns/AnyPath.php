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

use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;

/**
 * Represents the ANY construct.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/
 */
final class AnyPath implements CompletePattern
{
    use PatternTrait;

    /**
     * @var CompletePattern The pattern to match
     */
    private CompletePattern $pattern;

    /**
     * @param CompletePattern $pattern The pattern to find any path for
     */
    public function __construct(CompletePattern $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $cql = '';

        if (isset($this->variable)) {
            $cql = $this->variable->toQuery() . ' = ';
        }

        $cql .= sprintf("ANY (%s)", $this->pattern->toQuery());

        return $cql;
    }
}
