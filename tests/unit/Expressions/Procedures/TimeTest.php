<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Time
 */
final class TimeTest extends TestCase
{
    public function testToQuery(): void
    {
        $map = new Map(['foo'=>new String_('bar')]);

        $time = new Time($map);

        $this->assertSame("time({foo: 'bar'})", $time->toQuery());
    }

    public function testEmpty(): void
    {
        $time = new Time();

        $this->assertSame("time()", $time->toQuery());
    }

    public function testInstanceOfTimeType(): void
    {
        $time = new Time();

        $this->assertInstanceOf(TimeType::class, $time);
    }
}
