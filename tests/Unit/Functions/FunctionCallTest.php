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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Functions\All;
use WikibaseSolutions\CypherDSL\Functions\Any;
use WikibaseSolutions\CypherDSL\Functions\Exists;
use WikibaseSolutions\CypherDSL\Functions\FunctionCall;
use WikibaseSolutions\CypherDSL\Functions\IsEmpty;
use WikibaseSolutions\CypherDSL\Functions\None;
use WikibaseSolutions\CypherDSL\Functions\RawFunction;
use WikibaseSolutions\CypherDSL\Functions\Single;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Functions\FunctionCall
 */
class FunctionCallTest extends TestCase
{
	use TestHelper;

	public function testRaw()
	{
		$raw = FunctionCall::raw("foo", []);

		$this->assertInstanceOf(RawFunction::class, $raw);
	}

	public function testAll()
	{
		$variable = $this->getQueryConvertableMock(Variable::class, "a");
		$list = $this->getQueryConvertableMock(ListType::class, "[]");
		$predicate = $this->getQueryConvertableMock(AnyType::class, "b");

		$all = FunctionCall::all($variable, $list, $predicate);

		$this->assertInstanceOf(All::class, $all);
	}

	public function testAny()
	{
		$variable = $this->getQueryConvertableMock(Variable::class, "a");
		$list = $this->getQueryConvertableMock(ListType::class, "[]");
		$predicate = $this->getQueryConvertableMock(AnyType::class, "b");

		$any = FunctionCall::any($variable, $list, $predicate);

		$this->assertInstanceOf(Any::class, $any);
	}

	public function testExists()
	{
		$expression = $this->getQueryConvertableMock(AnyType::class, "a");

		$exists = FunctionCall::exists($expression);

		$this->assertInstanceOf(Exists::class, $exists);
	}

	public function testIsEmpty()
	{
		$list = $this->getQueryConvertableMock(ListType::class, "[]");

		$isEmpty = FunctionCall::isEmpty($list);

		$this->assertInstanceOf(IsEmpty::class, $isEmpty);
	}

	public function testNone()
	{
		$variable = $this->getQueryConvertableMock(Variable::class, "a");
		$list = $this->getQueryConvertableMock(ListType::class, "[]");
		$predicate = $this->getQueryConvertableMock(AnyType::class, "b");

		$none = FunctionCall::none($variable, $list, $predicate);

		$this->assertInstanceOf(None::class, $none);
	}

	public function testSingle()
	{
		$variable = $this->getQueryConvertableMock(Variable::class, "a");
		$list = $this->getQueryConvertableMock(ListType::class, "[]");
		$predicate = $this->getQueryConvertableMock(AnyType::class, "b");

		$single = FunctionCall::single($variable, $list, $predicate);

		$this->assertInstanceOf(Single::class, $single);
	}
}