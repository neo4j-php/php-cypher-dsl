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
use WikibaseSolutions\CypherDSL\Clauses\ReturnClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\ReturnClause
 */
class ReturnClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $return = new ReturnClause();

        $this->assertSame("", $return->toQuery());
        $this->assertSame([], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testSingleColumn(): void
    {
        $return = new ReturnClause();
        $column = $this->getQueryConvertibleMock(AnyType::class, "a");
        $return->addColumn($column);

        $this->assertSame("RETURN a", $return->toQuery());
        $this->assertSame([$column], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMultipleColumns(): void
    {
        $return = new ReturnClause();

        $columnA = $this->getQueryConvertibleMock(AnyType::class, "a");
        $columnB = $this->getQueryConvertibleMock(AnyType::class, "b");
        $columnC = $this->getQueryConvertibleMock(AnyType::class, "c");

        $return->addColumn($columnA);
        $return->addColumn($columnB);
        $return->addColumn($columnC);

        $this->assertSame("RETURN a, b, c", $return->toQuery());
        $this->assertSame([$columnA, $columnB, $columnC], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testSingleAlias(): void
    {
        $return = new ReturnClause();
        $column = $this->getQueryConvertibleMock(AnyType::class, "a");
        $return->addColumn($column, "b");

        $this->assertSame("RETURN a AS b", $return->toQuery());
        $this->assertSame(['b' => $column], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMultipleAliases(): void
    {
        $return = new ReturnClause();
        $columnA = $this->getQueryConvertibleMock(AnyType::class, "a");
        $columnB = $this->getQueryConvertibleMock(AnyType::class, "b");
        $return->addColumn($columnA, "b");
        $return->addColumn($columnB, "c");

        $this->assertSame("RETURN a AS b, b AS c", $return->toQuery());
        $this->assertSame(['b' => $columnA, 'c' => $columnB], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMixedAliases(): void
    {
        $return = new ReturnClause();
        $columnA = $this->getQueryConvertibleMock(AnyType::class, "a");
        $columnB = $this->getQueryConvertibleMock(AnyType::class, "c");
        $columnC = $this->getQueryConvertibleMock(AnyType::class, "b");
        $return->addColumn($columnA, "b");
        $return->addColumn($columnB);
        $return->addColumn($columnC, "c");

        $this->assertSame("RETURN a AS b, c, b AS c", $return->toQuery());
        $this->assertEquals(['b' => $columnA, $columnB, 'c' => $columnA], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testAliasIsEscaped(): void
    {
        $return = new ReturnClause();
        $column = $this->getQueryConvertibleMock(AnyType::class, "a");
        $return->addColumn($column, ":");

        $this->assertSame("RETURN a AS `:`", $return->toQuery());
        $this->assertSame([':' => $column], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testReturnDistinct(): void
    {
        $return = new ReturnClause();
        $column = $this->getQueryConvertibleMock(AnyType::class, "a");
        $return->addColumn($column);
        $return->setDistinct();

        $this->assertSame("RETURN DISTINCT a", $return->toQuery());
        $this->assertSame([$column], $return->getColumns());
        $this->assertTrue($return->isDistinct());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType(): void
    {
        $return = new ReturnClause();
        $return->addColumn($this->getQueryConvertibleMock(AnyType::class, "a"));
        $return->setDistinct();

        $return->toQuery();
    }
}
