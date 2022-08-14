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
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\DeleteClause
 */
class DeleteClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $delete = new DeleteClause();

        $this->assertSame("", $delete->toQuery());
        $this->assertEquals([], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testSingleVariable(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);

        $this->assertSame("DELETE a", $delete->toQuery());
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testMultipleVariables(): void
    {
        $delete = new DeleteClause();

        $a = new Variable('a');
        $b = new Variable('b');

        $delete->addStructure($a);
        $delete->addStructure($b);

        $this->assertSame("DELETE a, b", $delete->toQuery());
        $this->assertEquals([$a, $b], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDetachDelete(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE a", $delete->toQuery());
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertTrue($delete->detachesDeletion());
    }

    public function testAcceptsVariable(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);
        $delete->toQuery();
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $delete = new DeleteClause();
        $variable = $this->getQueryConvertibleMock(AnyType::class, "a");

        $this->expectException(TypeError::class);

        $delete->addStructure($variable);
        $delete->toQuery();
    }

    public function testSetVariables(): void
    {
        $delete = new DeleteClause();

        $variableA = new Variable('a');
        $variableB = new Variable('b');

        $variables = [$variableA, $variableB];

        $delete->setPatterns($variables);

        $this->assertSame("DELETE a, b", $delete->toQuery());
        $this->assertSame($variables, $delete->getStructural());
    }

    public function testSetVariablesDoesNotAcceptAnyType(): void
    {
        $delete = new DeleteClause();

        $variableA = new Variable('a');
        $variableB = $this->getQueryConvertibleMock(AnyType::class, "b");

        $variables = [$variableA, $variableB];

        $this->expectException(TypeError::class);

        $delete->setPatterns($variables);
        $delete->toQuery();
    }

    public function testGetVariables(): void
    {
        $delete = new DeleteClause();

        $this->assertSame([], $delete->getStructural());

        $variables = [new Variable('a')];
        $delete->setPatterns($variables);

        $this->assertSame($variables, $delete->getStructural());
    }

    public function testDetachesDeletion(): void
    {
        $delete = new DeleteClause();

        $this->assertFalse($delete->detachesDeletion());

        $delete->setDetach();

        $this->assertTrue($delete->detachesDeletion());
    }
}
