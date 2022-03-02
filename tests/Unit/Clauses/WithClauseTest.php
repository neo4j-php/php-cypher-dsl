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
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WithClause
 */
class WithClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause(): void
    {
        $return = new WithClause();

        $this->assertSame("", $return->toQuery());
        $this->assertEquals([], $return->getExpressions());
    }

    public function testSingleEntry(): void
    {
        $return = new WithClause();
        $entry = $this->getQueryConvertableMock(AnyType::class, "a");
        $return->addEntry($entry);

        $this->assertSame("WITH a", $return->toQuery());
        $this->assertEquals([$entry], $return->getExpressions());
    }

    public function testMultipleEntries(): void
    {
        $return = new WithClause();
        $entryA = $this->getQueryConvertableMock(AnyType::class, "a");
        $entryB = $this->getQueryConvertableMock(AnyType::class, "b");
        $entryC = $this->getQueryConvertableMock(AnyType::class, "c");

        $return->addEntry($entryA);
        $return->addEntry($entryB);
        $return->addEntry($entryC);

        $this->assertSame("WITH a, b, c", $return->toQuery());
        $this->assertEquals([$entryA, $entryB, $entryC], $return->getExpressions());
    }

    public function testSingleAlias(): void
    {
        $return = new WithClause();
        $entry = $this->getQueryConvertableMock(AnyType::class, "a");
        $return->addEntry($entry, "b");

        $this->assertSame("WITH a AS b", $return->toQuery());
        $this->assertEquals(['b' => $entry], $return->getExpressions());
    }

    public function testMultipleAliases(): void
    {
        $return = new WithClause();
        $entryA = $this->getQueryConvertableMock(AnyType::class, "a");
        $entryB = $this->getQueryConvertableMock(AnyType::class, "b");

        $return->addEntry($entryA, "b");
        $return->addEntry($entryB, "c");

        $this->assertSame("WITH a AS b, b AS c", $return->toQuery());
        $this->assertEquals(['b' => $entryA, 'c' => $entryB], $return->getExpressions());
    }

    public function testMixedAliases(): void
    {
        $return = new WithClause();
        $entryA = $this->getQueryConvertableMock(AnyType::class, "a");
        $entryB = $this->getQueryConvertableMock(AnyType::class, "c");
        $entryC = $this->getQueryConvertableMock(AnyType::class, "b");

        $return->addEntry($entryA, "b");
        $return->addEntry($entryB);
        $return->addEntry($entryC, "c");

        $this->assertSame("WITH a AS b, c, b AS c", $return->toQuery());
        $this->assertEquals(['b' => $entryA, $entryB, 'c' => $entryC], $return->getExpressions());
    }

    public function testAliasIsEscaped(): void
    {
        $return = new WithClause();
        $entry = $this->getQueryConvertableMock(AnyType::class, "a");
        $return->addEntry($entry, ":");

        $this->assertSame("WITH a AS `:`", $return->toQuery());
        $this->assertEquals([':' => $entry], $return->getExpressions());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType(): void
    {
        $return = new WithClause();
        $return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), ":");

        $return->toQuery();
    }
}
