<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction
 */
class ExclusiveDisjunctionTest extends TestCase
{
    public function testToQuery(): void
    {
        $xor = new ExclusiveDisjunction(new Boolean(true), new Boolean(false));

        $this->assertSame("(true XOR false)", $xor->toQuery());

        $xor = new ExclusiveDisjunction($xor, $xor);

        $this->assertSame("((true XOR false) XOR (true XOR false))", $xor->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $xor = new ExclusiveDisjunction(new Boolean(true), new Boolean(false), false);

        $this->assertSame("true XOR false", $xor->toQuery());

        $xor = new ExclusiveDisjunction($xor, $xor);

        $this->assertSame("(true XOR false XOR true XOR false)", $xor->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $and = new ExclusiveDisjunction($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $and->toQuery();
    }
}
