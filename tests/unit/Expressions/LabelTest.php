<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Label
 */
final class LabelTest extends TestCase
{
    public function testSingle(): void
    {
        $expression = new Variable("foo");
        $label = "Bar";

        $label = new Label($expression, $label);

        $this->assertSame("foo:Bar", $label->toQuery());
    }

    public function testMultiple(): void
    {
        $expression = new Variable("foo");
        $label = ["Bar", "Baz"];

        $label = new Label($expression, ...$label);

        $this->assertSame("foo:Bar:Baz", $label->toQuery());
    }

    public function testLabelIsEscaped(): void
    {
        $expression = new Variable("foo");
        $label = "{}";

        $label = new Label($expression, $label);

        $this->assertSame("foo:`{}`", $label->toQuery());
    }
}
