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
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Time
 */
class TimeTest extends TestCase
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

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType(): void
    {
        $map = $this->createMock(AnyType::class);

        $time = new Time($map);

        $time->toQuery();
    }
}
