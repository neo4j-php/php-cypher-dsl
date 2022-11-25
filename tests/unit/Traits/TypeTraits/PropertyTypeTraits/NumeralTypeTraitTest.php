<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits\PropertyTypeTraits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ModuloDivision;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait
 */
final class NumeralTypeTraitTest extends TestCase
{
    private NumeralType $a;
    private NumeralType $b;

    protected function setUp(): void
    {
        $this->a = new class() implements NumeralType
        {
            use NumeralTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = new Integer(15);
    }

    public function testPlus(): void
    {
        $plus = $this->a->plus($this->b);

        $this->assertInstanceOf(Addition::class, $plus);

        $this->assertTrue($plus->insertsParentheses());
        $this->assertEquals($this->a, $plus->getLeft());
        $this->assertEquals($this->b, $plus->getRight());
    }

    public function testPlusLiteral(): void
    {
        $plus = $this->a->plus(1);

        $this->assertInstanceOf(Addition::class, $plus);
    }

    public function testPlusNoParentheses(): void
    {
        $plus = $this->a->plus($this->b, false);

        $this->assertInstanceOf(Addition::class, $plus);

        $this->assertFalse($plus->insertsParentheses());
        $this->assertEquals($this->a, $plus->getLeft());
        $this->assertEquals($this->b, $plus->getRight());
    }

    public function testDivide(): void
    {
        $divide = $this->a->divide($this->b);

        $this->assertInstanceOf(Division::class, $divide);

        $this->assertTrue($divide->insertsParentheses());
        $this->assertEquals($this->a, $divide->getLeft());
        $this->assertEquals($this->b, $divide->getRight());
    }

    public function testDivideLiteral(): void
    {
        $divide = $this->a->divide(1);

        $this->assertInstanceOf(Division::class, $divide);
    }

    public function testDivideNoParentheses(): void
    {
        $divide = $this->a->divide($this->b, false);

        $this->assertInstanceOf(Division::class, $divide);

        $this->assertFalse($divide->insertsParentheses());
        $this->assertEquals($this->a, $divide->getLeft());
        $this->assertEquals($this->b, $divide->getRight());
    }

    public function testExponentiate(): void
    {
        $exponentiate = $this->a->exponentiate($this->b);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);

        $this->assertTrue($exponentiate->insertsParentheses());
        $this->assertEquals($this->a, $exponentiate->getLeft());
        $this->assertEquals($this->b, $exponentiate->getRight());
    }

    public function testExponentiateLiteral(): void
    {
        $exponentiate = $this->a->exponentiate(1);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);
    }

    public function testExponentiateNoParentheses(): void
    {
        $exponentiate = $this->a->exponentiate($this->b, false);

        $this->assertInstanceOf(Exponentiation::class, $exponentiate);

        $this->assertFalse($exponentiate->insertsParentheses());
        $this->assertEquals($this->a, $exponentiate->getLeft());
        $this->assertEquals($this->b, $exponentiate->getRight());
    }

    public function testMod(): void
    {
        $mod = $this->a->mod($this->b);

        $this->assertInstanceOf(ModuloDivision::class, $mod);

        $this->assertTrue($mod->insertsParentheses());
        $this->assertEquals($this->a, $mod->getLeft());
        $this->assertEquals($this->b, $mod->getRight());
    }

    public function testModLiteral(): void
    {
        $mod = $this->a->mod(1);

        $this->assertInstanceOf(ModuloDivision::class, $mod);
    }

    public function testModNoParentheses(): void
    {
        $mod = $this->a->mod($this->b, false);

        $this->assertInstanceOf(ModuloDivision::class, $mod);

        $this->assertFalse($mod->insertsParentheses());
        $this->assertEquals($this->a, $mod->getLeft());
        $this->assertEquals($this->b, $mod->getRight());
    }

    public function testTimes(): void
    {
        $times = $this->a->times($this->b);

        $this->assertInstanceOf(Multiplication::class, $times);

        $this->assertTrue($times->insertsParentheses());
        $this->assertEquals($this->a, $times->getLeft());
        $this->assertEquals($this->b, $times->getRight());
    }

    public function testTimesLiteral(): void
    {
        $times = $this->a->times(1);

        $this->assertInstanceOf(Multiplication::class, $times);
    }

    public function testTimesNoParentheses(): void
    {
        $times = $this->a->times($this->b, false);

        $this->assertInstanceOf(Multiplication::class, $times);

        $this->assertFalse($times->insertsParentheses());
        $this->assertEquals($this->a, $times->getLeft());
        $this->assertEquals($this->b, $times->getRight());
    }

    public function testMinus(): void
    {
        $minus = $this->a->minus($this->b);

        $this->assertInstanceOf(Subtraction::class, $minus);

        $this->assertTrue($minus->insertsParentheses());
        $this->assertEquals($this->a, $minus->getLeft());
        $this->assertEquals($this->b, $minus->getRight());
    }

    public function testMinusLiteral(): void
    {
        $minus = $this->a->minus(1);

        $this->assertInstanceOf(Subtraction::class, $minus);
    }

    public function testMinusNoParentheses(): void
    {
        $minus = $this->a->minus($this->b, false);

        $this->assertInstanceOf(Subtraction::class, $minus);

        $this->assertFalse($minus->insertsParentheses());
        $this->assertEquals($this->a, $minus->getLeft());
        $this->assertEquals($this->b, $minus->getRight());
    }

    public function testNegate(): void
    {
        $negate = $this->a->negate();

        $this->assertInstanceOf(UnaryMinus::class, $negate);
    }
}
