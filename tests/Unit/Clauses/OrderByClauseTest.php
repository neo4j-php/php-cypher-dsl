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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\OrderByClause;
use WikibaseSolutions\CypherDSL\Property;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\OrderByClause
 */
class OrderByClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $orderBy = new OrderByClause();

        $this->assertSame("", $orderBy->toQuery());
        $this->assertEquals([], $orderBy->getProperties());
        $this->assertFalse($orderBy->isDescending());
    }

    public function testSingleProperty(): void
    {
        $orderBy = new OrderByClause();
        $property = $this->getQueryConvertibleMock(Property::class, "a.a");
        $orderBy->addProperty($property);

        $this->assertSame("ORDER BY a.a", $orderBy->toQuery());
        $this->assertEquals([$property], $orderBy->getProperties());
        $this->assertFalse($orderBy->isDescending());
    }

    public function testMultipleProperties(): void
    {
        $orderBy = new OrderByClause();
        $propertyA = $this->getQueryConvertibleMock(Property::class, "a.a");
        $propertyB = $this->getQueryConvertibleMock(Property::class, "a.b");

        $orderBy->addProperty($propertyA);
        $orderBy->addProperty($propertyB);

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
        $this->assertEquals([$propertyA, $propertyB], $orderBy->getProperties());
        $this->assertFalse($orderBy->isDescending());
    }

    public function testSinglePropertyDesc(): void
    {
        $orderBy = new OrderByClause();
        $property = $this->getQueryConvertibleMock(Property::class, "a.a");
        $orderBy->addProperty($property);
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());
        $this->assertEquals([$property], $orderBy->getProperties());
        $this->assertTrue($orderBy->isDescending());
    }

    public function testMultiplePropertiesDesc(): void
    {
        $orderBy = new OrderByClause();
        $propertyA = $this->getQueryConvertibleMock(Property::class, "a.a");
        $propertyB = $this->getQueryConvertibleMock(Property::class, "a.b");

        $orderBy->addProperty($propertyA);
        $orderBy->addProperty($propertyB);
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a, a.b DESCENDING", $orderBy->toQuery());
        $this->assertEquals([$propertyA, $propertyB], $orderBy->getProperties());
        $this->assertTrue($orderBy->isDescending());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsProperty(): void
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertibleMock(Property::class, "a.a"));

        $orderBy->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $orderBy = new OrderByClause();

        $this->expectException(TypeError::class);

        $orderBy->addProperty($this->getQueryConvertibleMock(AnyType::class, "a.a"));

        $orderBy->toQuery();
    }
}
