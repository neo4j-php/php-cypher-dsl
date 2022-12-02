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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Negation
 */
final class NegationTest extends TestCase
{
    public function testToQuery(): void
    {
        $not = new Negation(new Boolean(true));

        $this->assertSame("(NOT true)", $not->toQuery());

        $not = new Negation($not);

        $this->assertSame("(NOT (NOT true))", $not->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $not = new Negation(new Boolean(true), false);

        $this->assertSame("NOT true", $not->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $not = new Negation(new Boolean(true));

        $this->assertInstanceOf(BooleanType::class, $not);
    }
}
