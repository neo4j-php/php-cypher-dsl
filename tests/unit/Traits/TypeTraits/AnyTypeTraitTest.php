<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait
 */
final class AnyTypeTraitTest extends TestCase
{
    private AnyType $a;
    private AnyType $b;

    protected function setUp(): void
    {
        $this->a = new class() implements AnyType
        {
            use AnyTypeTrait;

            public function toQuery(): string
            {
                return '420';
            }
        };

        $this->b = new class() implements AnyType
        {
            use AnyTypeTrait;

            public function toQuery(): string
            {
                return '1337';
            }
        };
    }

    public function testAlias(): void
    {
        $b = Query::variable('b');
        $alias = $this->a->alias($b);

        $this->assertInstanceOf(Alias::class, $alias);
        $this->assertSame($this->a, $alias->getOriginal());
        $this->assertSame($b, $alias->getVariable());
    }

    public function testAliasLiteral(): void
    {
        $alias = $this->a->alias('b');

        $this->assertInstanceOf(Alias::class, $alias);
        $this->assertSame($this->a, $alias->getOriginal());
        $this->assertEquals(new Variable('b'), $alias->getVariable());
    }

    public function testGt(): void
    {
        $gt = $this->a->gt($this->b);

        $this->assertInstanceOf(GreaterThan::class, $gt);

        $this->assertSame($this->a, $gt->getLeft());
        $this->assertSame($this->b, $gt->getRight());
    }

    public function testGtLiteral(): void
    {
        $gt = $this->a->gt(10);

        $this->assertInstanceOf(GreaterThan::class, $gt);

        $this->assertSame($this->a, $gt->getLeft());
        $this->assertEquals(new Integer(10), $gt->getRight());
    }

    public function testGte(): void
    {
        $gte = $this->a->gte($this->b);

        $this->assertInstanceOf(GreaterThanOrEqual::class, $gte);

        $this->assertEquals($this->a, $gte->getLeft());
        $this->assertEquals($this->b, $gte->getRight());
    }

    public function testGteLiteral(): void
    {
        $gte = $this->a->gte(10);

        $this->assertInstanceOf(GreaterThanOrEqual::class, $gte);

        $this->assertSame($this->a, $gte->getLeft());
        $this->assertEquals(new Integer(10), $gte->getRight());
    }

    public function testLt(): void
    {
        $lt = $this->a->lt($this->b);

        $this->assertInstanceOf(LessThan::class, $lt);

        $this->assertEquals($this->a, $lt->getLeft());
        $this->assertEquals($this->b, $lt->getRight());
    }

    public function testLtLiteral(): void
    {
        $lt = $this->a->lt(10);

        $this->assertInstanceOf(LessThan::class, $lt);

        $this->assertSame($this->a, $lt->getLeft());
        $this->assertEquals(new Integer(10), $lt->getRight());
    }

    public function testLte(): void
    {
        $lte = $this->a->lte($this->b);

        $this->assertInstanceOf(LessThanOrEqual::class, $lte);

        $this->assertEquals($this->a, $lte->getLeft());
        $this->assertEquals($this->b, $lte->getRight());
    }

    public function testLteLiteral(): void
    {
        $lte = $this->a->lte(10);

        $this->assertInstanceOf(LessThanOrEqual::class, $lte);

        $this->assertSame($this->a, $lte->getLeft());
        $this->assertEquals(new Integer(10), $lte->getRight());
    }

    public function testEquals(): void
    {
        $equals = $this->a->equals($this->b);

        $this->assertInstanceOf(Equality::class, $equals);

        $this->assertEquals($this->a, $equals->getLeft());
        $this->assertEquals($this->b, $equals->getRight());
    }

    public function testEqualsLiteral(): void
    {
        $equals = $this->a->equals(10);

        $this->assertInstanceOf(Equality::class, $equals);

        $this->assertSame($this->a, $equals->getLeft());
        $this->assertEquals(new Integer(10), $equals->getRight());
    }

    public function testNotEquals(): void
    {
        $notEquals = $this->a->notEquals($this->b);

        $this->assertInstanceOf(Inequality::class, $notEquals);

        $this->assertEquals($this->a, $notEquals->getLeft());
        $this->assertEquals($this->b, $notEquals->getRight());
    }

    public function testNotEqualsLiteral(): void
    {
        $notEquals = $this->a->notEquals(10);

        $this->assertInstanceOf(Inequality::class, $notEquals);

        $this->assertSame($this->a, $notEquals->getLeft());
        $this->assertEquals(new Integer(10), $notEquals->getRight());
    }

    public function testIsNull(): void
    {
        $isNull = $this->a->isNull();

        $this->assertInstanceOf(IsNull::class, $isNull);

        $this->assertEquals($this->a, $isNull->getExpression());
    }

    public function testIsNotNull(): void
    {
        $isNotNull = $this->a->isNotNull();

        $this->assertInstanceOf(IsNotNull::class, $isNotNull);

        $this->assertEquals($this->a, $isNotNull->getExpression());
    }
}
