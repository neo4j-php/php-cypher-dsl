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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime
 */
class LocalDateTimeTest extends TestCase
{
    public function testToQuery(): void
    {
        $map = new Map(['foo' => new String_('bar')]);

        $dateTime = new LocalDateTime($map);

        $this->assertSame("localdatetime({foo: 'bar'})", $dateTime->toQuery());
    }

    public function testEmpty(): void
    {
        $dateTime = new LocalDateTime();

        $this->assertSame("localdatetime()", $dateTime->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType(): void
    {
        $map = $this->createMock(AnyType::class);

        $dateTime = new LocalDateTime($map);

        $dateTime->toQuery();
    }
}
