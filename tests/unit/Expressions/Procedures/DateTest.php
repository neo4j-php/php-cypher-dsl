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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Date
 */
final class DateTest extends TestCase
{
    public function testToQuery(): void
    {
        $map = new Map(['foo' => new String_('bar')]);

        $date = new Date($map);

        $this->assertSame("date({foo: 'bar'})", $date->toQuery());
    }

    public function testEmpty(): void
    {
        $date = new Date();

        $this->assertSame("date()", $date->toQuery());
    }

    public function testInstanceOfDateType(): void
    {
        $date = new Date();

        $this->assertInstanceOf(DateType::class, $date);
    }
}
