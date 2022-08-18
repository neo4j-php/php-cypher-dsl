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
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SetClause
 */
class SetClauseTest extends TestCase
{

    public function testEmptyClause(): void
    {
        $set = new SetClause();

        $this->assertSame("", $set->toQuery());
        $this->assertEquals([], $set->getExpressions());
    }

    public function testSinglePattern(): void
    {
        $set = new SetClause();
        $value = $this->createMock(PropertyType::class);
        $value->method('toQuery')->willReturn('a');
        $expression = new PropertyReplacement(
            new Property(new Variable('Foo'),'Bar'),
            $value
        );

        $set->add($expression);

        $this->assertSame("SET Foo.Bar = a", $set->toQuery());
        $this->assertEquals([$expression], $set->getExpressions());
    }

    public function testMultiplePattern(): void
    {
        $set = new SetClause();
        $foo = new Variable('Foo');
        $value = $this->createMock(PropertyType::class);
        $value->method('toQuery')->willReturn('a');
        $expressionA = new PropertyReplacement(
            new Property($foo,'Bar'),
            $value
        );
        $expressionB = new Label($foo,'Baz');

        $set->add($expressionA);
        $set->add($expressionB);

        $this->assertSame("SET Foo.Bar = a, Foo:Baz", $set->toQuery());
        $this->assertEquals([$expressionA, $expressionB], $set->getExpressions());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsPropertyReplacement(): void
    {
        $set = new SetClause();
        $value = $this->createMock(PropertyType::class);
        $value->method('toQuery')->willReturn('a');
        $expression = new PropertyReplacement(
            new Property(new Variable('Foo'),'Bar'),
            $value
        );

        $set->add($expression);

        $set->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsLabel(): void
    {
        $set = new SetClause();
        $expression = new Label(new Variable('Foo'),'Baz');

        $set->add($expression);

        $set->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $set = new SetClause();
        $expression = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $set->add($expression);

        $set->toQuery();
    }

    public function testCanBeEmpty(): void
    {
        $clause = new SetClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
