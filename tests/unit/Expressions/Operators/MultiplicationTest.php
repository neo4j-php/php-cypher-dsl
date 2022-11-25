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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication
 */
class MultiplicationTest extends TestCase
{
    public function testToQuery(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15));

        $this->assertSame("(10 * 15)", $multiplication->toQuery());

        $multiplication = new Multiplication($multiplication, $multiplication);

        $this->assertSame("((10 * 15) * (10 * 15))", $multiplication->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15), false);

        $this->assertSame("10 * 15", $multiplication->toQuery());

        $multiplication = new Multiplication($multiplication, $multiplication);

        $this->assertSame("(10 * 15 * 10 * 15)", $multiplication->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperands(): void
    {
        $this->expectException(TypeError::class);

        $multiplication = new Multiplication($this->createMock(AnyType::class), $this->createMock(AnyType::class));

        $multiplication->toQuery();
    }
}