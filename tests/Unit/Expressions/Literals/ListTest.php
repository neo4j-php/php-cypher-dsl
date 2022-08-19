<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\List_
 */
final class ListTest extends TestCase
{
    public function testEmpty(): void
    {
        $list = new List_([]);

        $this->assertSame("[]", $list->toQuery());
    }

    public function testRequiresAnyTypeInConstructor(): void
    {
        $a = new class () {};

        $this->expectException(TypeError::class);

        new List_([$a]);
    }

    public function testDefaultListIsEmpty(): void
    {
        $list = new List_();

        $this->assertSame('[]', $list->toQuery());
    }

    public function testAddExpression(): void
    {
        $list = new List_();

        $list->addExpression(Query::float(1.0));

        $this->assertSame('[1.0]', $list->toQuery());
    }

    public function testAddExpressionLiteral(): void
    {
        $list = new List_();

        $list->addExpression(1.0);

        $this->assertSame('[1.0]', $list->toQuery());
    }

    public function testAddPatternIsCastedToVariable(): void
    {
        $list = new List_();
        $pattern = Query::node()->withVariable('foobar');

        $list->addExpression($pattern);

        $this->assertSame('[foobar]', $list->toQuery());

        $pattern = Query::node();

        $list->addExpression($pattern);

        $this->assertStringMatchesFormat('[foobar, var%s]', $list->toQuery());
    }

    public function testGetExpressions(): void
    {
        $list = new List_();

        $a = Query::literal(10);

        $list->addExpression($a);

        $this->assertSame([$a], $list->getExpressions());

        $b = Query::literal(11);

        $list->addExpression($b);

        $this->assertSame([$a, $b], $list->getExpressions());
    }

    public function testAddExpressionMultipleSingleCall(): void
    {
        $list = new List_();

        $a = Query::literal(10);
        $b = Query::literal(11);

        $list->addExpression($a, $b);

        $this->assertSame('[10, 11]', $list->toQuery());
    }

    public function testIsEmpty(): void
    {
        $list = new List_();

        $this->assertTrue($list->isEmpty());

        $list->addExpression(10);

        $this->assertFalse($list->isEmpty());
    }

    public function testInstanceOfListType(): void
    {
        $list = new List_();

        $this->assertInstanceOf(ListType::class, $list);
    }

    /**
     * @dataProvider provideOneDimensionalData
     * @param array $expressions
     * @param string $expected
     */
    public function testOneDimensional(array $expressions, string $expected): void
    {
        $list = new List_($expressions);

        $this->assertSame($expected, $list->toQuery());
    }

    /**
     * @dataProvider provideMultidimensionalData
     * @param array $expressions
     * @param string $expected
     */
    public function testMultidimensional(array $expressions, string $expected): void
    {
        $list = new List_($expressions);

        $this->assertSame($expected, $list->toQuery());
    }

    public function provideOneDimensionalData(): array
    {
        return [
            [[Query::literal(12)], "[12]"],
            [[Query::literal('12')], "['12']"],
            [[Query::literal('12'), Query::literal('13')], "['12', '13']"],
        ];
    }

    public function provideMultidimensionalData(): array
    {
        return [
            [[new List_([Query::literal(12)])], "[[12]]"],
            [[new List_([Query::literal('12')])], "[['12']]"],
            [[new List_([Query::literal('12'), Query::literal('14')]), new List_([Query::literal('13')])], "[['12', '14'], ['13']]"],
        ];
    }
}
