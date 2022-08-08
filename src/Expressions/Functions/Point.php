<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;

/**
 * This class represents the "point()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/spatial/
 * @see Func::point()
 *
 * @internal This class is not covered by the backwards compatibility promise of php-cypher-dsl
 */
final class Point extends Func implements PointType
{
    use PointTypeTrait;

    /**
     * @var MapType The map to use for constructing the point
     */
    private MapType $map;

    /**
     * The signature of the "point()" function is:
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
