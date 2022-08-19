<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\None
 */
class NoneTest extends TestCase
{

    public function testToQuery()
    {
        $variable = new Variable("variable");
        $list = new List_([new String_('foo'), new String_('bar')]);
        $predicate = $this->createMock(AnyType::class);
        $predicate->method('toQuery')->willReturn('predicate');

        $all = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN ['foo', 'bar'] WHERE predicate)", $all->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable()
    {
        $variable = $this->createMock(AnyType::class);
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsList()
    {
        $variable = new Variable("variable");
        $list = $this->createMock(AnyType::class);
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $all = new None($variable, $list, $predicate);

        $all->toQuery();
    }
}
