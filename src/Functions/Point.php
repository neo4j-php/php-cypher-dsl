<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Functions;

use WikibaseSolutions\CypherDSL\Traits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;

/**
 * This class represents the "point()" function.
 *
 * @note You most likely do not want to use this function directly. You probably want to use the Literal
 * class to construct these objects for you.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/
 */
class Point extends FunctionCall implements PointType
{
    use PointTypeTrait;

    /**
     * @var MapType The map to use for constructing the point
     */
    private MapType $map;

    /**
     * Point constructor. The signature of the "point()" function is:
     *
     * point(input :: MAP?) :: (POINT?) - returns a point object
     *
     * @param MapType $map The map to use for constructing the point
     */
    public function __construct(MapType $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "point(%s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->map];
    }
}
