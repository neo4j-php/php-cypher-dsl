<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime
 */
class LocalTimeTest extends TestCase
{

    public function testToQuery()
    {
        $map = new Map(['foo' => new String_('bar')]);

        $time = new LocalTime($map);

        $this->assertSame("localtime({foo: 'bar'})", $time->toQuery());
    }

    public function testEmpty()
    {
        $time = new LocalTime();

        $this->assertSame("localtime()", $time->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType()
    {
        $map = $this->createMock(AnyType::class);

        $time = new LocalTime($map);

        $time->toQuery();
    }
}
