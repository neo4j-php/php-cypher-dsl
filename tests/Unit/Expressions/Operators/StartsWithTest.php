<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith
 */
class StartsWithTest extends TestCase
{
    public function testToQuery(): void
    {
        $startsWith = new StartsWith(new Variable("a"), new String_("b"));

        $this->assertSame("(a STARTS WITH 'b')", $startsWith->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $startsWith = new StartsWith(new Variable("a"), new String_("b"), false);

        $this->assertSame("a STARTS WITH 'b'", $startsWith->toQuery());
    }

    public function testCannotBeNested(): void
    {
        $startsWith = new StartsWith(new Variable("a"), new String_("b"));

        $this->expectException(TypeError::class);

        $startsWith = new StartsWith($startsWith, $startsWith);

        $startsWith->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $startsWith = new StartsWith($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $startsWith->toQuery();
    }
}
