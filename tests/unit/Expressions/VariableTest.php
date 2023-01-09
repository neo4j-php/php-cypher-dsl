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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Variable
 */
final class VariableTest extends TestCase
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

    public function testGetNameReturnsEscaped(): void
    {
        $variable = new Variable('$foo');

        $this->assertSame('`$foo`', $variable->getName());
    }

    public function testEmptyNameIsNotAllowed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Variable('');
    }

    public function testLongNameIsNotAllowed(): void
    {
        $name = str_repeat('a', 65535);

        $this->expectException(InvalidArgumentException::class);

        new Variable($name);
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
            ['$foo', '`$foo`'],
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
