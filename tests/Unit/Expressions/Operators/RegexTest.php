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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Regex
 */
class RegexTest extends TestCase
{
    public function testToQuery(): void
    {
        $regex = new Regex(new Variable("a"), new String_("b"));

        $this->assertSame("(a =~ 'b')", $regex->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $regex = new Regex(new Variable("a"), new String_("b"), false);

        $this->assertSame("a =~ 'b'", $regex->toQuery());
    }

    public function testCannotBeNested(): void
    {
        $regex = new Regex(new Variable("a"), new String_("b"));

        $this->expectException(TypeError::class);

        $regex = new Regex($regex, $regex);

        $regex->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $regex = new Regex($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $regex->toQuery();
    }
}
