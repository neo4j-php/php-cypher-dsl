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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction
 */
final class SubtractionTest extends TestCase
{
    public function testToQuery(): void
    {
        $subtraction = new Subtraction(new Integer(10), new Integer(15));

        $this->assertSame("(10 - 15)", $subtraction->toQuery());

        $subtraction = new Subtraction($subtraction, $subtraction);

        $this->assertSame("((10 - 15) - (10 - 15))", $subtraction->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $subtraction = new Subtraction(new Integer(10), new Integer(15), false);

        $this->assertSame("10 - 15", $subtraction->toQuery());

        $subtraction = new Subtraction($subtraction, $subtraction);

        $this->assertSame("(10 - 15 - 10 - 15)", $subtraction->toQuery());
    }

    public function testInstanceOfIntegerType(): void
    {
        $subtraction = new Subtraction(new Integer(10), new Integer(15));

        $this->assertInstanceOf(IntegerType::class, $subtraction);
    }

    public function testInstanceOfFloatType(): void
    {
        $subtraction = new Subtraction(new Integer(10), new Integer(15));

        $this->assertInstanceOf(FloatType::class, $subtraction);
    }
}
