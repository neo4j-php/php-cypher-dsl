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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait
 */
final class StringTypeTraitTest extends TestCase
{
    private StringType $a;
    private StringType $b;

    protected function setUp(): void
    {
        $this->a = new class() implements StringType
        {
            use StringTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->b = new String_('15');
    }

    public function testContains(): void
    {
        $contains = $this->a->contains($this->b);

        $this->assertInstanceOf(Contains::class, $contains);

        $this->assertTrue($contains->insertsParentheses());
        $this->assertEquals($this->a, $contains->getLeft());
        $this->assertEquals($this->b, $contains->getRight());
    }

    public function testContainsLiteral(): void
    {
        $contains = $this->a->contains('test');

        $this->assertInstanceOf(Contains::class, $contains);
    }

    public function testContainsNoParentheses(): void
    {
        $contains = $this->a->contains($this->b, false);

        $this->assertInstanceOf(Contains::class, $contains);

        $this->assertFalse($contains->insertsParentheses());
        $this->assertEquals($this->a, $contains->getLeft());
        $this->assertEquals($this->b, $contains->getRight());
    }

    public function testEndsWith(): void
    {
        $endsWith = $this->a->endsWith($this->b);

        $this->assertInstanceOf(EndsWith::class, $endsWith);

        $this->assertTrue($endsWith->insertsParentheses());
        $this->assertEquals($this->a, $endsWith->getLeft());
        $this->assertEquals($this->b, $endsWith->getRight());
    }

    public function testEndsWithLiteral(): void
    {
        $endsWith = $this->a->endsWith('test');

        $this->assertInstanceOf(EndsWith::class, $endsWith);
    }

    public function testEndsWithNoParentheses(): void
    {
        $endsWith = $this->a->endsWith($this->b, false);

        $this->assertInstanceOf(EndsWith::class, $endsWith);

        $this->assertFalse($endsWith->insertsParentheses());
        $this->assertEquals($this->a, $endsWith->getLeft());
        $this->assertEquals($this->b, $endsWith->getRight());
    }

    public function testStartsWith(): void
    {
        $startsWith = $this->a->startsWith($this->b);

        $this->assertInstanceOf(StartsWith::class, $startsWith);

        $this->assertTrue($startsWith->insertsParentheses());
        $this->assertEquals($this->a, $startsWith->getLeft());
        $this->assertEquals($this->b, $startsWith->getRight());
    }

    public function testStartsWithLiteral(): void
    {
        $startsWith = $this->a->startsWith('test');

        $this->assertInstanceOf(StartsWith::class, $startsWith);
    }

    public function testStartsWithNoParentheses(): void
    {
        $startsWith = $this->a->startsWith($this->b, false);

        $this->assertInstanceOf(StartsWith::class, $startsWith);

        $this->assertFalse($startsWith->insertsParentheses());
        $this->assertEquals($this->a, $startsWith->getLeft());
        $this->assertEquals($this->b, $startsWith->getRight());
    }

    public function testRegex(): void
    {
        $regex = $this->a->regex($this->b);

        $this->assertInstanceOf(Regex::class, $regex);

        $this->assertTrue($regex->insertsParentheses());
        $this->assertEquals($this->a, $regex->getLeft());
        $this->assertEquals($this->b, $regex->getRight());
    }

    public function testRegexLiteral(): void
    {
        $regex = $this->a->regex('/test/');

        $this->assertInstanceOf(Regex::class, $regex);
    }

    public function testRegexNoParentheses(): void
    {
        $regex = $this->a->regex($this->b, false);

        $this->assertInstanceOf(Regex::class, $regex);

        $this->assertFalse($regex->insertsParentheses());
        $this->assertEquals($this->a, $regex->getLeft());
        $this->assertEquals($this->b, $regex->getRight());
    }
}
