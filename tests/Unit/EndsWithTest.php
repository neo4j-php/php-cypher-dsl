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

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Contains;
use WikibaseSolutions\CypherDSL\EndsWith;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\EndsWith
 */
class EndsWithTest extends TestCase
{
    use TestHelper;

	public function testToQuery()
	{
		$endsWith = new EndsWith($this->getQueryConvertableMock(StringType::class, "a"), $this->getQueryConvertableMock(StringType::class, "b"));

		$this->assertSame("(a ENDS WITH b)", $endsWith->toQuery());
	}

	public function testCannotBeNested()
	{
		$endsWith = new EndsWith($this->getQueryConvertableMock(StringType::class, "a"), $this->getQueryConvertableMock(StringType::class, "b"));

		$this->expectException(TypeError::class);

		$endsWith = new EndsWith($endsWith, $endsWith);

		$endsWith->toQuery();
	}

	public function testDoesNotAcceptAnyTypeAsOperands()
	{
		$this->expectException(TypeError::class);

		$endsWith = new EndsWith($this->getQueryConvertableMock(AnyType::class, "a"), $this->getQueryConvertableMock(AnyType::class, "b"));

		$endsWith->toQuery();
	}
}