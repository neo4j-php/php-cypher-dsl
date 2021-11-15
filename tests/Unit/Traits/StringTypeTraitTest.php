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
use WikibaseSolutions\CypherDSL\Contains;
use WikibaseSolutions\CypherDSL\EndsWith;
use WikibaseSolutions\CypherDSL\StartsWith;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\StringTypeTrait
 */
class StringTypeTraitTest extends TestCase
{
	use TestHelper;

	/**
	 * @var MockObject|StringType
	 */
	private $a;

	/**
	 * @var MockObject|StringType
	 */
	private $b;

	public function setUp(): void
	{
		$this->a = $this->getQueryConvertableMock(StringType::class, "10");
		$this->b = $this->getQueryConvertableMock(StringType::class, "15");
	}

	public function testContains()
	{
		$contains = $this->a->contains($this->b);

		$this->assertInstanceOf(Contains::class, $contains);
	}

	public function testEndsWith()
	{
		$endsWith = $this->a->endsWith($this->b);

		$this->assertInstanceOf(EndsWith::class, $endsWith);
	}

	public function testStartsWith()
	{
		$startsWith = $this->a->startsWith($this->b);

		$this->assertInstanceOf(StartsWith::class, $startsWith);
	}
}