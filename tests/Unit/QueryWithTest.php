<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "with" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryWithTest extends TestCase
{
    public function testWith()
    {
        $with = Query::new()->with('a')->with(['b' => new Variable('c')]);
        $this->assertSame('WITH a WITH c AS b', $with->toQuery());
    }
}
