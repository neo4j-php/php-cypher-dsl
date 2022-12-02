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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime
 */
final class LocalTimeTest extends TestCase
{
    public function testToQuery(): void
    {
        $map = Query::map(['foo' => 'bar']);
        $time = new LocalTime($map);

        $this->assertSame("localtime({foo: 'bar'})", $time->toQuery());
    }

    public function testEmpty(): void
    {
        $time = new LocalTime();

        $this->assertSame("localtime()", $time->toQuery());
    }

    public function testInstanceOfLocalTimeType(): void
    {
        $dateTime = new LocalTime();

        $this->assertInstanceOf(LocalTimeType::class, $dateTime);
    }
}
