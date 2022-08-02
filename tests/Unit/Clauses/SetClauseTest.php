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
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SetClause
 */
class SetClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $set = new SetClause();

        $this->assertSame("", $set->toQuery());
        $this->assertEquals([], $set->getExpressions());
    }

    public function testSinglePattern(): void
    {
        $set = new SetClause();
        $expression = $this->getQueryConvertibleMock(PropertyReplacement::class, "(a)");

        $set->addAssignment($expression);

        $this->assertSame("SET (a)", $set->toQuery());
        $this->assertEquals([$expression], $set->getExpressions());
    }

    public function testMultiplePattern(): void
    {
        $set = new SetClause();
        $expressionA = $this->getQueryConvertibleMock(PropertyReplacement::class, "(a)");
        $expressionB = $this->getQueryConvertibleMock(PropertyReplacement::class, "(b)");

        $set->addAssignment($expressionA);
        $set->addAssignment($expressionB);

        $this->assertSame("SET (a), (b)", $set->toQuery());
        $this->assertEquals([$expressionA, $expressionB], $set->getExpressions());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAssignment(): void
    {
        $set = new SetClause();
        $expression = $this->getQueryConvertibleMock(PropertyReplacement::class, "(a)");

        $set->addAssignment($expression);

        $set->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsLabel(): void
    {
        $set = new SetClause();
        $expression = $this->getQueryConvertibleMock(Label::class, "(a)");

        $set->addAssignment($expression);

        $set->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $set = new SetClause();
        $expression = $this->getQueryConvertibleMock(AnyType::class, "(a)");

        $this->expectException(TypeError::class);

        $set->addAssignment($expression);

        $set->toQuery();
    }
}
