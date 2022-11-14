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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction
 */
final class ConjunctionTest extends TestCase
{
    public function testToQuery(): void
    {
        $and = new Conjunction(new Boolean(true), new Boolean(false));

        $this->assertSame("(true AND false)", $and->toQuery());

        $and = new Conjunction($and, $and);

        $this->assertSame("((true AND false) AND (true AND false))", $and->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $and = new Conjunction(new Boolean(true), new Boolean(false), false);

        $this->assertSame("true AND false", $and->toQuery());

        $and = new Conjunction($and, $and);

        $this->assertSame("(true AND false AND true AND false)", $and->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $and = new Conjunction(new Boolean(true), new Boolean(false));

        $this->assertInstanceOf(BooleanType::class, $and);
    }
}
