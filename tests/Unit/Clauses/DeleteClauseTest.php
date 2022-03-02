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
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;

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
        $this->assertEquals([], $delete->getNodes());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testSingleNode(): void
    {
        $delete = new DeleteClause();
        $node = $this->getQueryConvertableMock(NodeType::class, "(a)");

        $delete->addNode($node);

        $this->assertSame("DELETE (a)", $delete->toQuery());
        $this->assertEquals([$node], $delete->getNodes());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testMultipleNodes(): void
    {
        $delete = new DeleteClause();

        $a = $this->getQueryConvertableMock(NodeType::class, "(a)");
        $b = $this->getQueryConvertableMock(NodeType::class, "(b)");

        $delete->addNode($a);
        $delete->addNode($b);

        $this->assertSame("DELETE (a), (b)", $delete->toQuery());
        $this->assertEquals([$a, $b], $delete->getNodes());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDetachDelete(): void
    {
        $delete = new DeleteClause();
        $pattern = $this->getQueryConvertableMock(NodeType::class, "(a)");

        $delete->addNode($pattern);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE (a)", $delete->toQuery());
        $this->assertEquals([$pattern], $delete->getNodes());
        $this->assertTrue($delete->detachesDeletion());
    }

    public function testAcceptsNodeType(): void
    {
        $delete = new DeleteClause();
        $pattern = $this->getQueryConvertableMock(NodeType::class, "(a)");

        $delete->addNode($pattern);
        $delete->toQuery();
        $this->assertEquals([$pattern], $delete->getNodes());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $delete = new DeleteClause();
        $pattern = $this->getQueryConvertableMock(AnyType::class, "(a)");

        $this->expectException(TypeError::class);

        $delete->addNode($pattern);
        $delete->toQuery();
    }
}
