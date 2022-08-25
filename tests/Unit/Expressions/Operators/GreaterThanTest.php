<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan
 */
final class GreaterThanTest extends TestCase
{

    public function testToQuery(): void
    {
        $greaterThan = new GreaterThan(new Integer(10), new Integer(15));

        $this->assertSame("(10 > 15)", $greaterThan->toQuery());

        $greaterThan = new GreaterThan($greaterThan, $greaterThan, false);

        $this->assertSame("(10 > 15) > (10 > 15)", $greaterThan->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $greaterThan = new GreaterThan(new Integer(10), new Integer(15), false);

        $this->assertSame("10 > 15", $greaterThan->toQuery());
    }

    public function testInstanceOfNumeralType(): void
    {
        $and = new GreaterThan(new Integer(1), new Integer(1));

        $this->assertInstanceOf(FloatType::class, $and);
        $this->assertInstanceOf(IntegerType::class, $and);
    }
}
