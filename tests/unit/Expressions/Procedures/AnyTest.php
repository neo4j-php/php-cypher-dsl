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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Any;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Any
 */
class AnyTest extends TestCase
{
    public function testToQuery(): void
    {
        $variable = new Variable("variable");
        $list = new List_([new String_('a'), new String_('b')]);
        $predicate = $this->createMock(AnyType::class);
        $predicate->method('toQuery')->willReturn('predicate');

        $any = new Any($variable, $list, $predicate);

        $this->assertSame("any(variable IN ['a', 'b'] WHERE predicate)", $any->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsVariable(): void
    {
        $variable = $this->createMock(AnyType::class);
        $list = new Variable("list");
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $any = new Any($variable, $list, $predicate);

        $any->toQuery();
    }

    public function testDoesNotAcceptAnyTypeAsList(): void
    {
        $variable = new Variable("variable");
        $list = $this->createMock(AnyType::class);
        $predicate = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $any = new Any($variable, $list, $predicate);

        $any->toQuery();
    }
}
