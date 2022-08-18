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
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RemoveClause
 */
class RemoveClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
        $this->assertEquals([], $remove->getExpressions());
    }

    public function testSingleExpression(): void
    {
        $remove = new RemoveClause();
        $expression = new Property(new Variable('Foo'),'Bar');

        $remove->addExpression($expression);

        $this->assertSame("REMOVE Foo.Bar", $remove->toQuery());
        $this->assertEquals([$expression], $remove->getExpressions());
    }

    public function testMultipleExpressions(): void
    {
        $remove = new RemoveClause();

        $a = new Property(new Variable('Foo'), 'Bar');
        $b = new Label(new Variable('a'), 'B');

        $remove->addExpression($a);
        $remove->addExpression($b);

        $this->assertSame("REMOVE Foo.Bar, a:B", $remove->toQuery());
        $this->assertEquals([$a, $b], $remove->getExpressions());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsProperty(): void
    {
        $remove = new RemoveClause();
        $expression = new Property(new Variable('Foo'),'Bar');

        $remove->addExpression($expression);

        $remove->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsLabel(): void
    {
        $remove = new RemoveClause();
        $expression = new Label(new Variable('a'), 'B');

        $remove->addExpression($expression);

        $remove->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $remove = new RemoveClause();
        $expression = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $remove->addExpression($expression);

        $remove->toQuery();
    }

    public function testCanBeEmpty(): void
    {
        $clause = new RemoveClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
