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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Division
 */
final class DivisionTest extends TestCase
{
    public function testToQuery(): void
    {
        $division = new Division(new Integer(10), new Integer(15));

        $this->assertSame("10 / 15", $division->toQuery());

        $division = new Division($division, $division);

        $this->assertSame("(10 / 15) / (10 / 15)", $division->toQuery());
    }

    public function testInstanceOfFloatType(): void
    {
        $division = new Division(Literal::integer(10), Literal::integer(10));

        $this->assertInstanceOf(FloatType::class, $division);
    }

    public function testInstanceOfIntegerType(): void
    {
        $division = new Division(Literal::integer(10), Literal::integer(10));

        $this->assertInstanceOf(IntegerType::class, $division);
    }
}
