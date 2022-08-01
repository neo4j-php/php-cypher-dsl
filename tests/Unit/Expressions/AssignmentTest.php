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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\PropertyReplacement
 */
class AssignmentTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $assignment = new PropertyReplacement($this->getQueryConvertibleMock(Property::class, "foo.bar"), $this->getQueryConvertibleMock(AnyType::class, "true"));

        $this->assertSame("foo.bar = true", $assignment->toQuery());

        $assignment->setMutate();

        $this->assertSame("foo.bar += true", $assignment->toQuery());
    }

    public function testLeftDoesNotAcceptAnyType()
    {
        $this->expectException(TypeError::class);

        $assignment = new PropertyReplacement($this->getQueryConvertibleMock(AnyType::class, "foo.bar"), $this->getQueryConvertibleMock(AnyType::class, "true"));

        $assignment->toQuery();
    }

    public function testLeftAcceptsProperty()
    {
        $assignment = new PropertyReplacement($this->getQueryConvertibleMock(Property::class, "foo.bar"), $this->getQueryConvertibleMock(AnyType::class, "true"));

        $this->assertSame("foo.bar = true", $assignment->toQuery());
    }

    public function testLeftAcceptsVariable()
    {
        $assignment = new PropertyReplacement($this->getQueryConvertibleMock(Variable::class, "foo.bar"), $this->getQueryConvertibleMock(AnyType::class, "true"));

        $this->assertSame("foo.bar = true", $assignment->toQuery());
    }
}
