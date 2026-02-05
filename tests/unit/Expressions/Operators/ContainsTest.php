<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Contains
 */
final class ContainsTest extends TestCase
{
    public function testToQuery(): void
    {
        $contains = new Contains(new Variable("a"), new String_("b"));

        $this->assertSame("a CONTAINS 'b'", $contains->toQuery());
    }

    public function testCannotBeNested(): void
    {
        $contains = new Contains(new Variable("a"), new String_("b"));

        $this->expectException(TypeError::class);

        new Contains($contains, $contains);
    }

    public function testInstanceOfBooleanType(): void
    {
        $a = Query::variable('a');
        $b = Literal::string('foo');

        $contains = new Contains($a, $b);

        $this->assertInstanceOf(BooleanType::class, $contains);
    }
}
