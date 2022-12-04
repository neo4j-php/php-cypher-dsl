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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\All;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Any;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Date;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\DateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalDateTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\LocalTime;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Point;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Raw;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Single;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Time;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure
 */
final class ProcedureTest extends TestCase
{
    public function testRaw(): void
    {
        $raw = Procedure::raw("foo", []);

        $this->assertInstanceOf(Raw::class, $raw);
    }

    public function testAll(): void
    {
        $variable = Query::variable('a');
        $list = Query::list([]);
        $predicate = Query::boolean(true);

        $all = Procedure::all($variable, $list, $predicate);

        $this->assertInstanceOf(All::class, $all);
    }

    public function testAny(): void
    {
        $variable = Query::variable('a');
        $list = Query::list([]);
        $predicate = Query::boolean(true);

        $any = Procedure::any($variable, $list, $predicate);

        $this->assertInstanceOf(Any::class, $any);
    }

    public function testExists(): void
    {
        $expression = Query::string("Hello World");

        $exists = Procedure::exists($expression);

        $this->assertInstanceOf(Exists::class, $exists);
    }

    public function testIsEmpty(): void
    {
        $list = Query::list([]);

        $isEmpty = Procedure::isEmpty($list);

        $this->assertInstanceOf(IsEmpty::class, $isEmpty);
    }

    public function testNone(): void
    {
        $variable = Query::variable('a');
        $list = Query::list([]);
        $predicate = Query::boolean(true);

        $none = Procedure::none($variable, $list, $predicate);

        $this->assertInstanceOf(None::class, $none);
    }

    public function testSingle(): void
    {
        $variable = Query::variable('a');
        $list = Query::list([]);
        $predicate = Query::boolean(true);

        $single = Procedure::single($variable, $list, $predicate);

        $this->assertInstanceOf(Single::class, $single);
    }

    public function testPoint(): void
    {
        $map = Query::map([]);

        $point = Procedure::point($map);

        $this->assertInstanceOf(Point::class, $point);
    }

    public function testDate(): void
    {
        $value = Query::string("Hello World!");

        $date = Procedure::date($value);

        $this->assertInstanceOf(Date::class, $date);

        $date = Procedure::date();

        $this->assertInstanceOf(Date::class, $date);
    }

    public function testDateTime(): void
    {
        $value = Query::string("Hello World!");

        $date = Procedure::datetime($value);

        $this->assertInstanceOf(DateTime::class, $date);

        $date = Procedure::datetime();

        $this->assertInstanceOf(DateTime::class, $date);
    }

    public function testLocalDateTime(): void
    {
        $value = Query::string("Hello World!");

        $date = Procedure::localdatetime($value);

        $this->assertInstanceOf(LocalDateTime::class, $date);

        $date = Procedure::localdatetime();

        $this->assertInstanceOf(LocalDateTime::class, $date);
    }

    public function testLocalTime(): void
    {
        $value = Query::string("Hello World!");

        $date = Procedure::localtime($value);

        $this->assertInstanceOf(LocalTime::class, $date);

        $date = Procedure::localtime();

        $this->assertInstanceOf(LocalTime::class, $date);
    }

    public function testTime(): void
    {
        $value = Query::string("Hello World!");

        $date = Procedure::time($value);

        $this->assertInstanceOf(Time::class, $date);

        $date = Procedure::time();

        $this->assertInstanceOf(Time::class, $date);
    }
}
