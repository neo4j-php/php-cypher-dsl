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
use WikibaseSolutions\CypherDSL\Order;
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
    }

    public function testSingleProperty(): void
    {
        $orderBy = new OrderByClause();
        $property = $this->getQueryConvertableMock(Property::class, "a.a");
        $orderBy->addProperty($property);

        $this->assertSame("ORDER BY a.a", $orderBy->toQuery());
        $this->assertEquals([$property], $orderBy->getProperties());
    }

    public function testMultipleProperties(): void
    {
        $orderBy = new OrderByClause();
        $propertyA = $this->getQueryConvertableMock(Property::class, "a.a");
        $propertyB = $this->getQueryConvertableMock(Property::class, "a.b");

        $orderBy->addProperty($propertyA);
        $orderBy->addProperty($propertyB);

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
        $this->assertEquals([$propertyA, $propertyB], $orderBy->getProperties());
    }

    public function testSinglePropertyDesc(): void
    {
        $orderBy = new OrderByClause();
        $property = $this->getQueryConvertableMock(Property::class, "a.a");
        $orderBy->addProperty($property, 'DESCENDING');

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());
        $this->assertEquals([$property], $orderBy->getProperties());
    }

    public function testMultiplePropertiesDesc(): void
    {
        $orderBy = new OrderByClause();
        $propertyA = $this->getQueryConvertableMock(Property::class, "a.a");
        $propertyB = $this->getQueryConvertableMock(Property::class, "a.b");

        $orderBy->addProperty($propertyA);
        $orderBy->addProperty($propertyB, 'DESCENDING');

        $this->assertSame("ORDER BY a.a, a.b DESCENDING", $orderBy->toQuery());
        $this->assertEquals([$propertyA, $propertyB], $orderBy->getProperties());
    }

    public function testMultiplePropertiesMixed(): void
    {
        $orderBy = new OrderByClause();
        $propertyA = $this->getQueryConvertableMock(Property::class, "a.a");
        $propertyB = $this->getQueryConvertableMock(Property::class, "a.b");

        $orderBy->addProperty($propertyA, 'ASC');
        $orderBy->addProperty($propertyB, 'DESCENDING');

        $this->assertSame("ORDER BY a.a ASC, a.b DESCENDING", $orderBy->toQuery());
        $this->assertEquals([$propertyA, $propertyB], $orderBy->getProperties());
        $this->assertEquals([new Order($propertyA, 'asc'), new Order($propertyB, 'descending')], $orderBy->getOrderings());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsProperty(): void
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));

        $orderBy->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $orderBy = new OrderByClause();

        $this->expectException(TypeError::class);

        $orderBy->addProperty($this->getQueryConvertableMock(AnyType::class, "a.a"));

        $orderBy->toQuery();
    }
}
