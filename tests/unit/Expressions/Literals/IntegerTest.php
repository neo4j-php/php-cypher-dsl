<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Integer
 */
final class IntegerTest extends TestCase
{
    public function testZero(): void
    {
        $decimal = new Integer(0);

        $this->assertSame("0", $decimal->toQuery());
        $this->assertEquals('0', $decimal->getValue());
    }

    public function testInstanceOfIntegerType(): void
    {
        $this->assertInstanceOf(IntegerType::class, new Integer(0));
    }

    public function testThrowsTypeErrorOnInvalidType(): void
    {
        $this->expectException(TypeError::class);

        new Integer([]);
    }

    public function testThrowsTypeErrorOnInvalidString(): void
    {
        $this->expectException(TypeError::class);

        new Integer('not an integer');
    }

    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery($number, string $expected): void
    {
        $decimal = new Integer($number);

        $this->assertSame($expected, $decimal->toQuery());
        $this->assertEquals($expected, $decimal->getValue());
    }

    /**
     * @dataProvider provideInvalidInputData
     */
    public function testInvalidInput($input): void
    {
        $this->expectException(TypeError::class);
        new Integer($input);
    }

    public function provideToQueryData(): array
    {
        return [
            [1, "1"],
            [2, "2"],
            ["2147483649", "2147483649"],
            ["9223372036854775816", "9223372036854775816"],
            [-12, "-12"],
            [69, "69"],
            ["292922929312312831203129382304823043284234729847294324724982749274294729427429471230918457", "292922929312312831203129382304823043284234729847294324724982749274294729427429471230918457"],
            ["-1238109438204130457284308235720483205", "-1238109438204130457284308235720483205"],
        ];
    }

    public function provideInvalidInputData(): array
    {
        return [
            ['nonumber'],
            ['12.3E36'],
            ['0x86'],
            ['5.5'],
        ];
    }
}
