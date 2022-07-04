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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Point;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\Point
 */
class PointTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $map = $this->getQueryConvertibleMock(MapType::class, "{latitude: toInteger('1'), longitude: toInteger('1')}");

        $point = new Point($map);

        $this->assertSame("point({latitude: toInteger('1'), longitude: toInteger('1')})", $point->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable()
    {
        $map = $this->getQueryConvertibleMock(AnyType::class, "map");

        $this->expectException(TypeError::class);

        $point = new Point($map);

        $point->toQuery();
    }
}
