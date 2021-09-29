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
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\DeleteClause
 */
class DeleteClauseTest extends TestCase
{
    use TestHelper;

    public function testEmptyClause()
    {
        $delete = new DeleteClause();

        $this->assertSame("", $delete->toQuery());
    }

    public function testSingleNode()
    {
        $delete = new DeleteClause();
        $node = $this->getExpressionMock("(a)", $this);

        $delete->addNode($node);

        $this->assertSame("DELETE (a)", $delete->toQuery());
    }

    public function testMultipleNodes()
    {
        $delete = new DeleteClause();

        $a = $this->getExpressionMock("(a)", $this);
        $b = $this->getExpressionMock("(b)", $this);

        $delete->addNode($a);
        $delete->addNode($b);

        $this->assertSame("DELETE (a), (b)", $delete->toQuery());
    }

    public function testDetachDelete()
    {
        $delete = new DeleteClause();
        $pattern = $this->getPatternMock("(a)", $this);

        $delete->addNode($pattern);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE (a)", $delete->toQuery());
    }
}