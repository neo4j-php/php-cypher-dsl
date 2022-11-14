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
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan
 */
class LessThanTest extends TestCase
{
    public function testToQuery(): void
    {
        $lessThan = new LessThan(new Integer(10), new Integer(15));

        $this->assertSame("(10 < 15)", $lessThan->toQuery());

        $lessThan = new LessThan($lessThan, $lessThan, false);

        $this->assertSame("(10 < 15) < (10 < 15)", $lessThan->toQuery());
    }

    public function testToQueryNoParentheses(): void
    {
        $lessThan = new LessThan(new Integer(10), new Integer(15), false);

        $this->assertSame("10 < 15", $lessThan->toQuery());
    }
}
