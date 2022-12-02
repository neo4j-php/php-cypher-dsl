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
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Any;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Any
 */
final class AnyTest extends TestCase
{
    public function testToQuery(): void
    {
        $variable = Query::variable('variable');
        $list = Query::list(['a', 'b']);

        $any = new Any($variable, $list, Query::boolean(true));

        $this->assertSame("any(variable IN ['a', 'b'] WHERE true)", $any->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $variable = Query::variable('variable');
        $list = Query::list(['a', 'b']);

        $any = new Any($variable, $list, Query::boolean(true));

        $this->assertInstanceOf(BooleanType::class, $any);
    }
}
