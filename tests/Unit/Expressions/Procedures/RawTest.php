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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw
 */
final class RawTest extends TestCase
{
    public function testToQuery()
    {
        $a = new Variable('a');
        $b = new Variable('b');
        $c = new Variable('c');

        $raw = new Raw("foobar", [$a, $b, $c]);

        $this->assertSame("foobar(a, b, c)", $raw->toQuery());
    }

    public function testRequiresAnyTypeParameters()
    {
        $a = new class () {};

        $this->expectException(TypeError::class);

        new Raw('foobar', [$a]);
    }

    public function testInstanceOf()
    {
        $raw = new Raw('foo', [Literal::string('foo')]);

        $this->assertInstanceOf(BooleanType::class, $raw);
        $this->assertInstanceOf(DateType::class, $raw);
        $this->assertInstanceOf(DateTimeType::class, $raw);
        $this->assertInstanceOf(FloatType::class, $raw);
        $this->assertInstanceOf(IntegerType::class, $raw);
        $this->assertInstanceOf(StringType::class, $raw);
        $this->assertInstanceOf(MapType::class, $raw);
        $this->assertInstanceOf(PointType::class, $raw);
        $this->assertInstanceOf(ListType::class, $raw);
        $this->assertInstanceOf(LocalDateTimeType::class, $raw);
        $this->assertInstanceOf(LocalTimeType::class, $raw);
        $this->assertInstanceOf(TimeType::class, $raw);
    }
}
