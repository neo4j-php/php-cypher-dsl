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
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Variable
 */
class VariableTest extends TestCase
{
    public function testEmptyConstructor(): void
    {
        $variable = new Variable();

        $this->assertMatchesRegularExpression('/var[0-9a-f]+/', $variable->toQuery());

        $variable = new Variable(null);

        $this->assertMatchesRegularExpression('/var[0-9a-f]+/', $variable->toQuery());
    }

    public function testLabeled(): void
    {
        $variable = new Variable();
        $label = $variable->labeled('hello', 'world');

        $this->assertInstanceOf(Label::class, $label);
        $this->assertSame(['hello', 'world'], $label->getLabels());
        $this->assertSame($variable, $label->getVariable());
    }

    public function testAssign(): void
    {
        $variable = new Variable();
        $map = Query::map(['foo' => 'bar', 'boo' => 'far']);
        $assignment = $variable->assign($map);

        $this->assertInstanceOf(PropertyReplacement::class, $assignment);
        $this->assertSame($variable, $assignment->getProperty());
        $this->assertSame($map, $assignment->getValue());
    }

    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(string $variable, string $expected): void
    {
        $variable = new Variable($variable);

        $this->assertSame($expected, $variable->toQuery());
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
