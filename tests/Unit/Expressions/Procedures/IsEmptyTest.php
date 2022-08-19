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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty
 */
class IsEmptyTest extends TestCase
{
    public function testToQuery()
    {
        $list = new List_([new String_('a'), new String_('b')]);

        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(['a', 'b'])", $isEmpty->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsListType()
    {
        $list = new List_;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsMapType()
    {
        $list = new Map;

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsStringType()
    {
        $list = new String_('a');

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }

    public function testDoestNotAcceptAnyType()
    {
        $list = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        $isEmpty = new IsEmpty($list);

        $isEmpty->toQuery();
    }
}
