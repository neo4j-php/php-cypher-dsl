<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Point
 */
class PointTest extends TestCase
{

    public function testToQuery()
    {
        $map = new Map(['latitude' => new Float_(1.5), 'longitude' => new Float_(4.2)]);

        $point = new Point($map);

        $this->assertSame("point({latitude: 1.5, longitude: 4.2})", $point->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable()
    {
        $map = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $point = new Point($map);

        $point->toQuery();
    }
}
