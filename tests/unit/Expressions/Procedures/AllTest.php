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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\All;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\All
 */
class AllTest extends TestCase
{
    public function testToQuery(): void
    {
        $variable = Query::variable('variable');
        $list = Literal::list(['a', 'b']);

        $all = new All($variable, $list, Literal::boolean(true));

        $this->assertSame("all(variable IN ['a', 'b'] WHERE true)", $all->toQuery());
    }
}
