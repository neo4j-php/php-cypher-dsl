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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\IsEmpty
 */
final class IsEmptyTest extends TestCase
{
    public function testToQuery(): void
    {
        $list = Query::list(['a', 'b']);
        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(['a', 'b'])", $isEmpty->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $list = Query::list(['a', 'b']);
        $isEmpty = new IsEmpty($list);

        $this->assertInstanceOf(BooleanType::class, $isEmpty);
    }
}
