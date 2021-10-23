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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause
 */
class OptionalMatchTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $match = new OptionalMatchClause();

        $this->assertSame("", $match->toQuery());
    }

    public function testSinglePattern()
    {
        $match = new OptionalMatchClause();
        $match->addPattern($this->getQueryConvertableMock(StructuralType::class, "(a)"));

        $this->assertSame("OPTIONAL MATCH (a)", $match->toQuery());
    }

    public function testMultiplePatterns()
    {
        $match = new OptionalMatchClause();
        $match->addPattern($this->getQueryConvertableMock(StructuralType::class, "(a)"));
        $match->addPattern($this->getQueryConvertableMock(StructuralType::class, "(b)"));

        $this->assertSame("OPTIONAL MATCH (a), (b)", $match->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsNodeType()
    {
        $match = new OptionalMatchClause();
        $match->addPattern($this->getQueryConvertableMock(NodeType::class, "(a)"));

        $match->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsPathType()
    {
        $match = new OptionalMatchClause();
        $match->addPattern($this->getQueryConvertableMock(PathType::class, "(a)"));

        $match->toQuery();
    }

    public function testDoesNotAcceptAnyType()
    {
        $match = new OptionalMatchClause();

        $this->expectException(\TypeError::class);

        $match->addPattern($this->getQueryConvertableMock(AnyType::class, "(a)"));

        $match->toQuery();
    }
}