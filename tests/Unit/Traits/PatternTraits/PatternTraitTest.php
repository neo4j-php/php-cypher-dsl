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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\PatternTraits;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait
 */
class PatternTraitTest extends TestCase
{
    /**
     * @var Pattern
     */
    private Pattern $stub;

    public function setUp(): void
    {
        $this->stub = new class () implements Pattern {
            use PatternTrait;
            public function toQuery(): string
            {
                return 'FooBar';
            }
        };
    }
    public function testSetVariable(): void
    {
        $this->stub->withVariable("hello");

        $this->assertSame("hello", $this->stub->getVariable()->getName());

        $variable = new Variable("hello");
        $this->stub->withVariable($variable);

        $this->assertSame($variable, $this->stub->getVariable());
    }

    public function testGetVariableWithoutVariable(): void
    {
        $variable = $this->stub->getVariable();

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertStringMatchesFormat("var%s", $variable->getName());
    }

	public function testDoesNotAcceptAnyType(): void
	{
		$this->expectException(TypeError::class);

		$this->stub->withVariable(new \stdClass());
	}
}
