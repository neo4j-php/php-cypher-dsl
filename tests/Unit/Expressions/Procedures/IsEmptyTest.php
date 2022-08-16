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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty
 */
class IsEmptyTest extends TestCase
{
    public function testToQuery()
    {
        $list = new List_([new String_('a'), new String_('b')]);

        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(['a', 'b'])", $isEmpty->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsListType()
    {
        $list = new List_;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsMapType()
    {
        $list = new Map;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsStringType()
    {
        $list = new String_('a');

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    public function testDoestNotAcceptAnyType()
    {
        $list = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }
}
