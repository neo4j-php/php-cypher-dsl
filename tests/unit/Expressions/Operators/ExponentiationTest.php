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
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation
 */
final class ExponentiationTest extends TestCase
{
    public function testToQuery(): void
    {
        $exponentiation = new Exponentiation(new Integer(10), new Integer(15));

        $this->assertSame("10 ^ 15", $exponentiation->toQuery());

        $exponentiation = new Exponentiation($exponentiation, $exponentiation);

        $this->assertSame("(10 ^ 15) ^ (10 ^ 15)", $exponentiation->toQuery());
    }

    public function testInstanceOfNumeralType(): void
    {
        $and = new Exponentiation(new Integer(1), new Integer(1));

        $this->assertInstanceOf(FloatType::class, $and);
        $this->assertInstanceOf(IntegerType::class, $and);
    }
}
