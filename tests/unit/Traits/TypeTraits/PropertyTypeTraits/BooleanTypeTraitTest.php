<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits\PropertyTypeTraits;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait
 */
final class BooleanTypeTraitTest extends TestCase
{
    /**
     * @var BooleanType|MockObject
     */
    private $a;

    /**
     * @var BooleanType|MockObject
     */
    private $b;

    protected function setUp(): void
    {
        $this->a = new class() implements BooleanType
        {
            use BooleanTypeTrait;

            public function toQuery(): string
            {
                return '';
            }
        };
        $this->b = new Boolean(false);
    }

    public function testAnd(): void
    {
        $and = $this->a->and($this->b);

        $this->assertInstanceOf(Conjunction::class, $and);

        $this->assertTrue($and->insertsParentheses());
        $this->assertEquals($this->a, $and->getLeft());
        $this->assertEquals($this->b, $and->getRight());
    }

    public function testAndLiteral(): void
    {
        $xor = $this->a->and(false);

        $this->assertInstanceOf(Conjunction::class, $xor);
    }

    public function testAndNoParentheses(): void
    {
        $and = $this->a->and($this->b, false);

        $this->assertInstanceOf(Conjunction::class, $and);

        $this->assertFalse($and->insertsParentheses());
        $this->assertEquals($this->a, $and->getLeft());
        $this->assertEquals($this->b, $and->getRight());
    }

    public function testOr(): void
    {
        $or = $this->a->or($this->b);

        $this->assertInstanceOf(Disjunction::class, $or);

        $this->assertTrue($or->insertsParentheses());
        $this->assertEquals($this->a, $or->getLeft());
        $this->assertEquals($this->b, $or->getRight());
    }

    public function testOrLiteral(): void
    {
        $xor = $this->a->or(false);

        $this->assertInstanceOf(Disjunction::class, $xor);
    }

    public function testOrNoParentheses(): void
    {
        $or = $this->a->or($this->b, false);

        $this->assertInstanceOf(Disjunction::class, $or);

        $this->assertFalse($or->insertsParentheses());
        $this->assertEquals($this->a, $or->getLeft());
        $this->assertEquals($this->b, $or->getRight());
    }

    public function testXor(): void
    {
        $xor = $this->a->xor($this->b);

        $this->assertInstanceOf(ExclusiveDisjunction::class, $xor);

        $this->assertTrue($xor->insertsParentheses());
        $this->assertEquals($this->a, $xor->getLeft());
        $this->assertEquals($this->b, $xor->getRight());
    }

    public function testXorLiteral(): void
    {
        $xor = $this->a->xor(false);

        $this->assertInstanceOf(ExclusiveDisjunction::class, $xor);
    }

    public function testXorNoParentheses(): void
    {
        $xor = $this->a->xor($this->b, false);

        $this->assertInstanceOf(ExclusiveDisjunction::class, $xor);

        $this->assertFalse($xor->insertsParentheses());
        $this->assertEquals($this->a, $xor->getLeft());
        $this->assertEquals($this->b, $xor->getRight());
    }

    public function testNot(): void
    {
        $not = $this->a->not();

        $this->assertInstanceOf(Negation::class, $not);
    }
}
