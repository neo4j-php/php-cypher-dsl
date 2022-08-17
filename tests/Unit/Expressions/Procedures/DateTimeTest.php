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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime
 */
class DateTimeTest extends TestCase
{
    public function testToQuery()
    {
        $map = new Map(['foo' => new String_('bar')]);

        $dateTime = new DateTime($map);

        $this->assertSame("datetime({foo: 'bar'})", $dateTime->toQuery());
    }

    public function testEmpty()
    {
        $dateTime = new DateTime();

        $this->assertSame("datetime()", $dateTime->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType()
    {
        $map = $this->createMock(AnyType::class);

        $dateTime = new DateTime($map);

        $dateTime->toQuery();
    }
}
