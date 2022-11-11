<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "remove" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryRemoveTest extends TestCase
{
	public function testSingleProperty(): void
    {
        $prop = Query::variable('hello')->property('world');

        $query = Query::new()->remove($prop);

        $this->assertSame('REMOVE hello.world', $query->toQuery());
    }

    public function testMultipleProperties(): void
    {
        $a = Query::variable('hello')->property('world');
        $b = Query::variable('world')->property('hello');

        $query = Query::new()->remove([$a, $b]);

        $this->assertSame('REMOVE hello.world, world.hello', $query->toQuery());
    }

    public function testSingleLabel(): void
    {
        $tom = Query::variable('tom')->labeled('Actor');

        $query = Query::new()->remove($tom);

        $this->assertSame('REMOVE tom:Actor', $query->toQuery());
    }

    public function testMultipleLabels(): void
    {
        $tom = Query::variable('tom')->labeled('Actor');
        $leonardo = Query::variable('leonardo')->labeled('Actor');

        $query = Query::new()->remove([$tom, $leonardo]);

        $this->assertSame('REMOVE tom:Actor, leonardo:Actor', $query->toQuery());
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->remove([Query::variable('tom')->labeled('Actor')]);

        $this->assertSame($expected, $actual);
    }
}
