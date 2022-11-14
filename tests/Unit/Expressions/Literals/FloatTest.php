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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Float_
 */
final class FloatTest extends TestCase
{
    public function testZero(): void
    {
        $float = new Float_(0);

        $this->assertSame("0.0", $float->toQuery());
    }

    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(float $value, string $expected): void
    {
        $float = new Float_($value);

        $this->assertSame($expected, $float->toQuery());
    }

    public function testGetValue(): void
    {
        $value = 1.01239;

        $float = new Float_($value);

        $this->assertSame($value, $float->getValue());
    }

    public function testInstanceOfFloatType(): void
    {
        $this->assertInstanceOf(FloatType::class, new Float_(1.0));
    }

    public function provideToQueryData(): array
    {
        return [
            [1, '1.0'],
            [1.0, '1.0'],
            [111111111111111, '1.1111111111111E+14'],
            [1337.1337, '1337.1337'],
        ];
    }
}
