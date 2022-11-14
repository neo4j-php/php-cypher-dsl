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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual
 */
final class GreaterThanOrEqualTest extends TestCase
{
    public function testToQuery(): void
    {
        $greaterThanOrEqual = new GreaterThanOrEqual(new Integer(10), new Integer(15));

        $this->assertSame("(10 >= 15)", $greaterThanOrEqual->toQuery());

        $greaterThanOrEqual = new GreaterThanOrEqual($greaterThanOrEqual, $greaterThanOrEqual);

        $this->assertSame("((10 >= 15) >= (10 >= 15))", $greaterThanOrEqual->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $greaterThanOrEqual = new GreaterThanOrEqual(new Integer(10), new Integer(15), false);

        $this->assertSame("10 >= 15", $greaterThanOrEqual->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $greaterThanOrEqual = new GreaterThanOrEqual(new Integer(1), new Integer(1));

        $this->assertInstanceOf(BooleanType::class, $greaterThanOrEqual);
    }
}
