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
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * Represents the SHORTEST k GROUPS construct.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/
 */
final class ShortestGroups implements CompletePattern
{
    use PatternTrait;

    /**
     * @var CompletePattern The pattern to match
     */
    private CompletePattern $pattern;

    /**
     * @var IntegerType The number of groups to match
     */
    private IntegerType $k;

    /**
     * @param CompletePattern $pattern The pattern to find the shortest groups for
     * @param int|IntegerType $k       The number of groups to match
     */
    public function __construct(CompletePattern $pattern, int|IntegerType $k)
    {
        $this->pattern = $pattern;
        $this->k = CastUtils::toIntegerType($k);
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

        $cql .= sprintf("SHORTEST %s GROUPS (%s)", $this->k->toQuery(), $this->pattern->toQuery());

        return $cql;
    }
}
