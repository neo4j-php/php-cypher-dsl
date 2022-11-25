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
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PropertyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PropertyTypeTrait
 */
final class PropertyTypeTraitTest extends TestCase
{
    private PropertyType $a;
    private List_ $list;

    protected function setUp(): void
    {
        $this->a = new class() implements PropertyType
        {
            use PropertyTypeTrait;

            public function toQuery(): string
            {
                return '10';
            }
        };
        $this->list = new List_([new String_('foobar')]);
    }

    public function testIn(): void
    {
        $in = $this->a->in($this->list);

        $this->assertInstanceOf(In::class, $in);

        $this->assertTrue($in->insertsParentheses());
        $this->assertEquals($this->a, $in->getLeft());
        $this->assertEquals($this->list, $in->getRight());
    }

    public function testInLiteral(): void
    {
        $in = $this->a->in(['a', 'b', 'c']);

        $this->assertInstanceOf(In::class, $in);
    }

    public function testInNoParentheses(): void
    {
        $in = $this->a->in($this->list, false);

        $this->assertInstanceOf(In::class, $in);

        $this->assertFalse($in->insertsParentheses());
        $this->assertEquals($this->a, $in->getLeft());
        $this->assertEquals($this->list, $in->getRight());
    }
}
