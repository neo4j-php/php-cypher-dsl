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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication
 */
final class MultiplicationTest extends TestCase
{
    public function testToQuery(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15));

        $this->assertSame("10 * 15", $multiplication->toQuery());

        $multiplication = new Multiplication($multiplication, $multiplication);

        $this->assertSame("(10 * 15) * (10 * 15)", $multiplication->toQuery());
    }

    public function testInstanceOfFloatType(): void
    {
        $multiplication = new Multiplication(new Float_(10.0), new Float_(15.0));

        $this->assertInstanceOf(FloatType::class, $multiplication);
    }

    public function testInstanceOfIntegerType(): void
    {
        $multiplication = new Multiplication(new Integer(10), new Integer(15));

        $this->assertInstanceOf(IntegerType::class, $multiplication);
    }
}
