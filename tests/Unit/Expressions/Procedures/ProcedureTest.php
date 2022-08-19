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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\All;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Any;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Single;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure
 */
final class ProcedureTest extends TestCase
{
    public function testRaw()
    {
        $raw = Procedure::raw("foo", []);

        $this->assertInstanceOf(Raw::class, $raw);
    }

    public function testAll()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $all = Procedure::all($variable, $list, $predicate);

        $this->assertInstanceOf(All::class, $all);
    }

    public function testAny()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $any = Procedure::any($variable, $list, $predicate);

        $this->assertInstanceOf(Any::class, $any);
    }

    public function testExists()
    {
        $expression = $this->createMock(AnyType::class);

        $exists = Procedure::exists($expression);

        $this->assertInstanceOf(Exists::class, $exists);
    }

    public function testIsEmpty()
    {
        $list = new List_;

        $isEmpty = Procedure::isEmpty($list);

        $this->assertInstanceOf(IsEmpty::class, $isEmpty);
    }

    public function testNone()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $none = Procedure::none($variable, $list, $predicate);

        $this->assertInstanceOf(None::class, $none);
    }

    public function testSingle()
    {
        $variable = new Variable("a");
        $list = new List_;
        $predicate = $this->createMock(AnyType::class);

        $single = Procedure::single($variable, $list, $predicate);

        $this->assertInstanceOf(Single::class, $single);
    }

    public function testPoint()
    {
        $map = new Map([]);

        $point = Procedure::point($map);

        $this->assertInstanceOf(Point::class, $point);
    }

    public function testDate()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::date($value);

        $this->assertInstanceOf(Date::class, $date);

        $date = Procedure::date();

        $this->assertInstanceOf(Date::class, $date);
    }

    public function testDateTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::datetime($value);

        $this->assertInstanceOf(DateTime::class, $date);

        $date = Procedure::datetime();

        $this->assertInstanceOf(DateTime::class, $date);
    }

    public function testLocalDateTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::localdatetime($value);

        $this->assertInstanceOf(LocalDateTime::class, $date);

        $date = Procedure::localdatetime();

        $this->assertInstanceOf(LocalDateTime::class, $date);
    }

    public function testLocalTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::localtime($value);

        $this->assertInstanceOf(LocalTime::class, $date);

        $date = Procedure::localtime();

        $this->assertInstanceOf(LocalTime::class, $date);
    }

    public function testTime()
    {
        $value = $this->createMock(AnyType::class);

        $date = Procedure::time($value);

        $this->assertInstanceOf(Time::class, $date);

        $date = Procedure::time();

        $this->assertInstanceOf(Time::class, $date);
    }

    public function testToQuery(): void
    {
        $mock = $this->getMockForAbstractClass(Procedure::class);
        $mock->method('getSignature')->willReturn('foo(%s)');
        $mock->method('getParameters')->willReturn([Literal::string('bar')]);

        $this->assertSame("foo('bar')", $mock->toQuery());
    }
}
