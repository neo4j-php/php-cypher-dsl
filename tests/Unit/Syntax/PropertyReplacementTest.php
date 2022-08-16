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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Syntax;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement
 */
class PropertyReplacementTest extends TestCase
{
    public function testToQuery()
    {
        $propRepl = new PropertyReplacement(new Property(new Variable('foo'), 'bar'), new Boolean(true));

        $this->assertSame("foo.bar = true", $propRepl->toQuery());

        $propRepl->setMutate();

        $this->assertSame("foo.bar += true", $propRepl->toQuery());
    }

    public function testLeftDoesNotAcceptAnyType()
    {
        $this->expectException(TypeError::class);

        $propRepl = new PropertyReplacement($this->createMock(AnyType::class), new Boolean(true));

        $propRepl->toQuery();
    }

    public function testLeftAcceptsProperty()
    {
        $propRepl = new PropertyReplacement(new Property(new Variable('foo'), 'bar'), new Boolean(true));

        $this->assertSame("foo.bar = true", $propRepl->toQuery());
    }

    public function testLeftAcceptsVariable()
    {
        $propRepl = new PropertyReplacement(new Variable('foo'), new Boolean(true));

        $this->assertSame("foo = true", $propRepl->toQuery());
    }
}
