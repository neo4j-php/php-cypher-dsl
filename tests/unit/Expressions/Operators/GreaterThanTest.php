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
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan
 */
final class GreaterThanTest extends TestCase
{
    public function testToQuery(): void
    {
        $greaterThan = new GreaterThan(new Integer(10), new Integer(15));

        $this->assertSame("10 > 15", $greaterThan->toQuery());

        $greaterThan = new GreaterThan($greaterThan, $greaterThan, false);

        $this->assertSame("10 > 15 > 10 > 15", $greaterThan->toQuery());
    }

    public function testChainedComparisons(): void
    {
        $a = new Integer(10);
        $b = new Integer(5);
        $c = new Integer(1);

        $expr = $a->gt($b)->gt($c);

        $this->assertSame("10 > 5 > 1", $expr->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $greaterThan = new GreaterThan(new Integer(1), new Integer(1));

        $this->assertInstanceOf(BooleanType::class, $greaterThan);
    }
}
