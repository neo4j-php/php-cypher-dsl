<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Point
 */
final class PointTest extends TestCase
{
    public function testToQuery(): void
    {
        $map = Query::map(['latitude' => 1.5, 'longitude' => 4.2]);
        $point = new Point($map);

        $this->assertSame("point({latitude: 1.5, longitude: 4.2})", $point->toQuery());
    }

    public function testInstanceOfPointType(): void
    {
        $map = Query::map(['latitude' => 1.5, 'longitude' => 4.2]);
        $point = new Point($map);

        $this->assertInstanceOf(PointType::class, $point);
    }
}
