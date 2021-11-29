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

    public function testEmptyClause()
    {
        $orderBy = new OrderByClause();

        $this->assertSame("", $orderBy->toQuery());
    }

    public function testSingleProperty()
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));

        $this->assertSame("ORDER BY a.a", $orderBy->toQuery());
    }

    public function testMultipleProperties()
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.b"));

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
    }

    public function testSinglePropertyDesc()
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());
    }

    public function testMultiplePropertiesDesc()
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.b"));
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a, a.b DESCENDING", $orderBy->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsProperty()
    {
        $orderBy = new OrderByClause();
        $orderBy->addProperty($this->getQueryConvertableMock(Property::class, "a.a"));

        $orderBy->toQuery();
    }

    public function testDoesNotAcceptAnyType()
    {
        $orderBy = new OrderByClause();

        $this->expectException(TypeError::class);

        $orderBy->addProperty($this->getQueryConvertableMock(AnyType::class, "a.a"));

        $orderBy->toQuery();
    }
}