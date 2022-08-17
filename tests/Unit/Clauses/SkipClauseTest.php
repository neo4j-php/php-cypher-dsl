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
use WikibaseSolutions\CypherDSL\Clauses\SkipClause;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\LimitClause
 */
class SkipClauseTest extends TestCase
{

    public function testEmptyClause(): void
    {
        $skip = new SkipClause();

        $this->assertSame("", $skip->toQuery());
        $this->assertNull($skip->getSkip());
    }

    public function testPattern(): void
    {
        $skip = new SkipClause();
        $expression = new Integer(10);

        $skip->setSkip($expression);

        $this->assertSame("SKIP 10", $skip->toQuery());
        $this->assertEquals($expression, $skip->getSkip());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsInteger(): void
    {
        $skip = new SkipClause();
        $expression = new Integer(10);

        $skip->setSkip($expression);

        $skip->toQuery();
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $skip = new SkipClause();
        $expression = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $skip->setSkip($expression);

        $skip->toQuery();
    }
}
