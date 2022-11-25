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
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual
 */
class LessThanOrEqualTest extends TestCase
{
    public function testToQuery(): void
    {
        $lessThanOrEqual = new LessThanOrEqual(new Integer(10), new Integer(15));

        $this->assertSame("(10 <= 15)", $lessThanOrEqual->toQuery());

        $lessThanOrEqual = new LessThanOrEqual($lessThanOrEqual, $lessThanOrEqual, false);

        $this->assertSame("(10 <= 15) <= (10 <= 15)", $lessThanOrEqual->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $lessThanOrEqual = new LessThanOrEqual(new Integer(10), new Integer(15), false);

        $this->assertSame("10 <= 15", $lessThanOrEqual->toQuery());
    }
}
