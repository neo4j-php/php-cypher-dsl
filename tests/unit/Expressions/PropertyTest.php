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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Property
 */
final class PropertyTest extends TestCase
{
    public static function provideToQueryData(): array
    {
        return [
            [new Variable("a"), "a", "a.a"],
            [new Map(["b" => new String_("c")]), "b", "{b: 'c'}.b"],
            [new Variable("b"), "a", "b.a"],
            [new Map([":" => new String_("c")]), ":", "{`:`: 'c'}.`:`"],
        ];
    }

    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(MapType $map, string $property, string $expected): void
    {
        $property = new Property($map, $property);

        $this->assertSame($expected, $property->toQuery());
    }

    public function testGetProperty(): void
    {
        $property = new Property(new Variable("a"), 'a');

        $this->assertSame('a', $property->getProperty());
    }

    public function testGetExpression(): void
    {
        $variable = new Variable('a');
        $property = new Property($variable, 'a');

        $this->assertSame($variable, $property->getExpression());
    }

    public function testReplaceWithReturnsPropertyReplacement(): void
    {
        $property = new Property(new Variable("a"), 'a');

        $this->assertInstanceOf(PropertyReplacement::class, $property->replaceWith(true));
    }

    public function testReplaceWithCastsPHPTypes(): void
    {
        $property = new Property(new Variable("a"), 'a');

        $bool = $property->replaceWith(true);
        $float = $property->replaceWith(1.0);
        $int = $property->replaceWith(10);
        $string = $property->replaceWith("foobar");

        $this->assertEquals(new Boolean(true), $bool->getValue());
        $this->assertEquals(new Float_(1.0), $float->getValue());
        $this->assertEquals(new Integer(10), $int->getValue());
        $this->assertEquals(new String_("foobar"), $string->getValue());
    }

    public function testReplaceWithUsesSameProperty(): void
    {
        $property = new Property(new Variable("a"), 'a');

        $replaceWith = $property->replaceWith("Hello World!");

        $this->assertSame($property, $replaceWith->getProperty());
    }
}
