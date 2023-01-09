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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\None;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\None
 */
final class NoneTest extends TestCase
{
    public function testToQuery(): void
    {
        $variable = Query::variable('variable');
        $list = Query::list(['foo', 'bar']);
        $predicate = Query::boolean(true);

        $none = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN ['foo', 'bar'] WHERE true)", $none->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $variable = Query::variable('variable');
        $list = Query::list(['foo', 'bar']);
        $predicate = Query::boolean(true);

        $none = new None($variable, $list, $predicate);

        $this->assertInstanceOf(BooleanType::class, $none);
    }
}
