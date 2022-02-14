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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\AndOperator;
use WikibaseSolutions\CypherDSL\Not;
use WikibaseSolutions\CypherDSL\OrOperator;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\XorOperator;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait
 */
class BooleanTypeTraitTest extends TestCase
{
    use TestHelper;

    /**
     * @var MockObject|BooleanType
     */
    private $a;

    /**
     * @var MockObject|BooleanType
     */
    private $b;

    public function setUp(): void
    {
        $this->a = $this->getQueryConvertableMock(BooleanType::class, "true");
        $this->b = $this->getQueryConvertableMock(BooleanType::class, "false");
    }

    public function testAnd()
    {
        $and = $this->a->and($this->b);

        $this->assertInstanceOf(AndOperator::class, $and);
    }

    public function testOr()
    {
        $or = $this->a->or($this->b);

        $this->assertInstanceOf(OrOperator::class, $or);
    }

    public function testXor()
    {
        $xor = $this->a->xor($this->b);

        $this->assertInstanceOf(XorOperator::class, $xor);
    }

    public function testNot()
    {
        $not = $this->a->not();

        $this->assertInstanceOf(Not::class, $not);
    }
}
