<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Single;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Single
 */
class SingleTest extends TestCase
{
    public function testToQuery(): void
    {
        $variable = new Variable("variable");
        $list = new List_([new String_('a'), new String_('b')]);
        $predicate = $this->createMock(AnyType::class);
        $predicate->method('toQuery')->willReturn('predicate');

        $all = new Single($variable, $list, $predicate);

        $this->assertSame("single(variable IN ['a', 'b'] WHERE predicate)", $all->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable(): void
    {
        $variable = $this->createMock(AnyType::class);
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new Single($variable, $list, $predicate);

        $all->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsList(): void
    {
        $variable = new Variable("variable");
        $list = $this->createMock(AnyType::class);
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new Single($variable, $list, $predicate);

        $all->toQuery();
    }
}