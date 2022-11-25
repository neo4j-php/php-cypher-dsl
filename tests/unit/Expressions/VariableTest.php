<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Variable
 */
class VariableTest extends TestCase
{
    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(string $variable, string $expected): void
    {
        $variable = new Variable($variable);

        $this->assertSame($expected, $variable->toQuery());
    }

    public function testEmptyConstructor(): void
    {
        $variable = new Variable();

        $this->assertMatchesRegularExpression('/var[0-9a-f]+/', $variable->toQuery());

        $variable = new Variable(null);

        $this->assertMatchesRegularExpression('/var[0-9a-f]+/', $variable->toQuery());
    }

    /**
     * @dataProvider providePropertyData
     */
    public function testProperty(string $variable, string $property, Property $expected): void
    {
        $variable = new Variable($variable);
        $property = $variable->property($property);

        $this->assertEquals($expected, $property);
    }

    public function provideToQueryData(): array
    {
        return [
            ["a", "a"],
            ["b", "b"],
        ];
    }

    public function providePropertyData(): array
    {
        return [
            ["a", "a", new Property(new Variable("a"), "a")],
            ["a", "b", new Property(new Variable("a"), "b")],
        ];
    }
}
