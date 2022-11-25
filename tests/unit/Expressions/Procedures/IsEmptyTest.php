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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty
 */
class IsEmptyTest extends TestCase
{
    public function testToQuery(): void
    {
        $list = new List_([new String_('a'), new String_('b')]);

        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(['a', 'b'])", $isEmpty->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsListType(): void
    {
        $list = new List_;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsMapType(): void
    {
        $list = new Map;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsStringType(): void
    {
        $list = new String_('a');

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    public function testDoestNotAcceptAnyType(): void
    {
        $list = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }
}
