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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime
 */
class DateTimeTest extends TestCase
{
    public function testToQuery()
    {
        $map = new Map(['foo' => new String_('bar')]);

        $dateTime = new DateTime($map);

        $this->assertSame("datetime({foo: 'bar'})", $dateTime->toQuery());
    }

    public function testEmpty()
    {
        $dateTime = new DateTime();

        $this->assertSame("datetime()", $dateTime->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType()
    {
        $map = $this->createMock(AnyType::class);

        $dateTime = new DateTime($map);

        $dateTime->toQuery();
    }
}
