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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Functions\Date;
use WikibaseSolutions\CypherDSL\Functions\Point;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Literals\Literal
 */
class LiteralTest extends TestCase
{
    use TestHelper;

    public function testLiteralString()
    {
        $string = Literal::literal('Testing is a virtue!');

        $this->assertInstanceOf(StringLiteral::class, $string);
    }

    public function testLiteralBoolean()
    {
        $boolean = Literal::literal(true);

        $this->assertInstanceOf(Boolean::class, $boolean);
    }

    public function testLiteralInteger()
    {
        $integer = Literal::literal(1);

        $this->assertInstanceOf(Decimal::class, $integer);
    }

    public function testLiteralFloat()
    {
        $float = Literal::literal(1.0);

        $this->assertInstanceOf(Decimal::class, $float);
    }

    public function testStringable()
    {
        $stringable = Literal::literal(new class {
            public function __toString()
            {
                return 'Testing is a virtue!';
            }
        });

        $this->assertInstanceOf(StringLiteral::class, $stringable);
    }

    public function testBoolean()
    {
       $boolean = Literal::boolean(true);

       $this->assertInstanceOf(Boolean::class, $boolean);

       $boolean = Literal::boolean(false);

       $this->assertInstanceOf(Boolean::class, $boolean);
    }

    public function testString()
    {
       $string = Literal::string('Testing is a virtue!');

       $this->assertInstanceOf(StringLiteral::class, $string);
    }

    public function testDecimal()
    {
       $decimal = Literal::decimal(1);

       $this->assertInstanceOf(Decimal::class, $decimal);

       $decimal = Literal::decimal(1.0);

       $this->assertInstanceOf(Decimal::class, $decimal);
    }

    public function testPoint2d()
    {
       $point = Literal::point2d(1, 2);

       $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "crs" => new StringLiteral("cartesian"), "srid" => new Decimal(7203)])), $point);

       $point = Literal::point2d(
           new Decimal(1),
           new Decimal(2)
       );

       $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "crs" => new StringLiteral("cartesian"), "srid" => new Decimal(7203)])), $point);
    }

    public function testPoint3d()
    {
       $point = Literal::point3d(1, 2, 3);

       $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "z" => new Decimal(3), "crs" => new StringLiteral("cartesian-3D"), "srid" => new Decimal(9157)])), $point);

       $point = Literal::point3d(
           new Decimal(1),
           new Decimal(2),
           new Decimal(3)
       );

       $this->assertEquals(new Point(new PropertyMap(["x" => new Decimal(1), "y" => new Decimal(2), "z" => new Decimal(3), "crs" => new StringLiteral("cartesian-3D"), "srid" => new Decimal(9157)])), $point);
    }

    public function testPoint2dWGS84()
    {
       $point = Literal::point2dWGS84(1, 2);

       $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "crs" => new StringLiteral("WGS-84"), "srid" => new Decimal(4326)])), $point);

       $point = Literal::point2dWGS84(
           new Decimal(1),
           new Decimal(2)
       );

       $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "crs" => new StringLiteral("WGS-84"), "srid" => new Decimal(4326)])), $point);
    }

    public function testPoint3dWGS84()
    {
       $point = Literal::point3dWGS84(1, 2, 3);

       $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "height" => new Decimal(3), "crs" => new StringLiteral("WGS-84-3D"), "srid" => new Decimal(4979)])), $point);

       $point = Literal::point3dWGS84(
           new Decimal(1),
           new Decimal(2),
           new Decimal(3)
       );

       $this->assertEquals(new Point(new PropertyMap(["longitude" => new Decimal(1), "latitude" => new Decimal(2), "height" => new Decimal(3), "crs" => new StringLiteral("WGS-84-3D"), "srid" => new Decimal(4979)])), $point);
    }

    public function testDate()
    {
       $date = Literal::date();

       $this->assertEquals(new Date(), $date);
    }

    public function testDateTimezone()
    {
       $date = Literal::date("Europe/Amsterdam");

       $this->assertEquals(new Date(new PropertyMap(["timezone" => new StringLiteral("Europe/Amsterdam")])), $date);

       $date = Literal::date(new StringLiteral("Europe/Amsterdam"));

       $this->assertEquals(new Date(new PropertyMap(["timezone" => new StringLiteral("Europe/Amsterdam")])), $date);
    }

    /**
     * @dataProvider provideDateYMDData
     * @param $year
     * @param $month
     * @param $day
     * @param $expected
     */
    public function testDateYMD($year, $month, $day, $expected)
    {
        $date = Literal::dateYMD($year, $month, $day);

        $this->assertEquals($expected, $date);
    }

    public function testDateYMDMissingMonth()
    {
       $this->expectException(\LogicException::class);

       $date = Literal::dateYMD(2000, null, 17);

       $date->toQuery();
    }

    /**
     * @dataProvider provideDateYWDData
     * @param $year
     * @param $week
     * @param $weekday
     * @param $expected
     */
    public function testDateYWD($year, $week, $weekday, $expected)
    {
        $date = Literal::dateYWD($year, $week, $weekday);

        $this->assertEquals($expected, $date);
    }

    public function testDateYWDMissingWeek()
    {
        $this->expectException(\LogicException::class);

        $date = Literal::dateYWD(2000, null, 17);

        $date->toQuery();
    }

    public function testDateString()
    {
        $date = Literal::dateString('2000-17-12');

        $this->assertEquals(new Date(new StringLiteral('2000-17-12')), $date);
    }

    public function provideDateYMDData()
    {
        return [
            [2000, null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [2000, 12, 17, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(17)]))],
            [new Decimal(2000), null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(17), new Date(new PropertyMap(["year" => new Decimal(2000), "month" => new Decimal(12), "day" => new Decimal(17)]))],

        ];
    }

    public function provideDateYWDData()
    {
        return [
            [2000, null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [2000, 12, null, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12)]))],
            [2000, 12, 17, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12), "dayOfWeek" => new Decimal(17)]))],
            [new Decimal(2000), null, null, new Date(new PropertyMap(["year" => new Decimal(2000)]))],
            [new Decimal(2000), new Decimal(12), null, new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12)]))],
            [new Decimal(2000), new Decimal(12), new Decimal(17), new Date(new PropertyMap(["year" => new Decimal(2000), "week" => new Decimal(12), "dayOfWeek" => new Decimal(17)]))],

        ];
    }
}