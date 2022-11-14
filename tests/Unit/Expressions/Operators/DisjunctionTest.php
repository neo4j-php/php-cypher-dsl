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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction
 */
final class DisjunctionTest extends TestCase
{
    public function testToQuery(): void
    {
        $or = new Disjunction(new Boolean(true), new Boolean(false));

        $this->assertSame("(true OR false)", $or->toQuery());

        $or = new Disjunction($or, $or);

        $this->assertSame("((true OR false) OR (true OR false))", $or->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $or = new Disjunction(new Boolean(true), new Boolean(false), false);

        $this->assertSame("true OR false", $or->toQuery());

        $or = new Disjunction($or, $or);

        $this->assertSame("(true OR false OR true OR false)", $or->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $or = new Disjunction(Literal::boolean(true), Literal::boolean(true));

        $this->assertInstanceOf(BooleanType::class, $or);
    }
}
