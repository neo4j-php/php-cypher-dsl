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
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus
 */
final class UnaryMinusTest extends TestCase
{
    public function testToQuery(): void
    {
        $minus = new UnaryMinus(new Integer(-10));

        $this->assertSame("(- -10)", $minus->toQuery());

        $minus = new UnaryMinus($minus);

        $this->assertSame("(- (- -10))", $minus->toQuery());

        $minus = new UnaryMinus(new Integer(10));

        $this->assertSame("(-10)", $minus->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $minus = new UnaryMinus(new Integer(10), false);

        $this->assertSame("-10", $minus->toQuery());
    }

    public function testInstanceOfFloatType(): void
    {
        $minus = new UnaryMinus(new Integer(-10));

        $this->assertInstanceOf(FloatType::class, $minus);
    }

    public function testInstanceOfIntegerType(): void
    {
        $minus = new UnaryMinus(new Integer(-10));

        $this->assertInstanceOf(IntegerType::class, $minus);
    }
}
