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
use WikibaseSolutions\CypherDSL\Label;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Label
 */
class LabelTest extends TestCase
{
    use TestHelper;

    public function testSingle()
    {
        $expression = $this->getQueryConvertableMock(Variable::class, "foo");
        $label = ["Bar"];

        $label = new Label($expression, $label);

        $this->assertSame("foo:Bar", $label->toQuery());
    }

    public function testMultiple()
    {
        $expression = $this->getQueryConvertableMock(Variable::class, "foo");
        $label = ["Bar", "Baz"];

        $label = new Label($expression, $label);

        $this->assertSame("foo:Bar:Baz", $label->toQuery());
    }

    public function testLabelIsEscaped()
    {
        $expression = $this->getQueryConvertableMock(Variable::class, "foo");
        $label = ["{}"];

        $label = new Label($expression, $label);

        $this->assertSame("foo:`{}`", $label->toQuery());
    }
}