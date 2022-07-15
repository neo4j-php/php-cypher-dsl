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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Patterns\Pattern
 */
class PatternTest extends TestCase
{
    public function testSetVariable(): void
    {
        $stub = $this->getMockForAbstractClass(Pattern::class);
        $stub->withVariable("hello");

        $this->assertSame("hello", $stub->getVariable()->getName());

        $variable = new Variable("hello");
        $stub->withVariable($variable);

        $this->assertSame($variable, $stub->getVariable());
    }

    public function testGetVariableWithoutVariable(): void
    {
        $stub = $this->getMockForAbstractClass(Pattern::class);
        $variable = $stub->getVariable();

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertStringMatchesFormat("var%s", $variable->getName());
    }

	public function testDoesNotAcceptAnyType(): void
	{
		$stub = $this->getMockForAbstractClass(Pattern::class);

		$this->expectException(TypeError::class);

		$stub->withVariable(new \stdClass());
	}
}
