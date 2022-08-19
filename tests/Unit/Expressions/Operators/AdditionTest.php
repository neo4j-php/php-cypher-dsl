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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Addition
 */
final class AdditionTest extends TestCase
{
    public function testToQuery(): void
    {
        $left = new Integer(10);
        $right = new Float_(15);
        $addition = new Addition($left, $right);

        $this->assertSame("(10 + 15.0)", $addition->toQuery());

        $this->assertSame($left, $addition->getLeft());
        $this->assertSame($right, $addition->getRight());

        $newAddition = new Addition($addition, $addition);

        $this->assertSame("((10 + 15.0) + (10 + 15.0))", $newAddition->toQuery());

        $this->assertTrue($newAddition->insertsParentheses());
        $this->assertEquals($addition, $newAddition->getLeft());
        $this->assertEquals($addition, $newAddition->getRight());

        $newAddition = new Addition($addition, $addition, false);

        $this->assertSame("(10 + 15.0) + (10 + 15.0)", $newAddition->toQuery());

        $this->assertFalse($newAddition->insertsParentheses());
        $this->assertEquals($addition, $newAddition->getLeft());
        $this->assertEquals($addition, $newAddition->getRight());
    }

    public function testInstanceOfFloatType(): void
    {
        $addition = new Addition(Literal::float(1), Literal::float(1));

        $this->assertInstanceOf(FloatType::class, $addition);
    }

    public function testInstanceOfIntegerType(): void
    {
        $addition = new Addition(Literal::float(1), Literal::float(1));

        $this->assertInstanceOf(IntegerType::class, $addition);
    }
}
