<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;

/**
 * Tests the "set" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QuerySetTest extends TestCase
{
    public function testSetSingleLabel(): void
    {
        $set = Query::new()->set(Query::variable('a')->labeled('foo'))->toQuery();

        $this->assertSame('SET a:foo', $set);
    }

    public function testSetMultipleLabels(): void
    {
        $set = Query::new()->set([Query::variable('a')->labeled('foo'), Query::variable('b')->labeled('foo')])->toQuery();

        $this->assertSame('SET a:foo, b:foo', $set);
    }

    public function testPropertyReplacement(): void
    {
        $set = Query::new()->set(Query::variable('a')->assign(['a' => 'b']))->toQuery();

        $this->assertSame('SET a = {a: \'b\'}', $set);
    }

    public function testPropertyReplacementSingleProperty(): void
    {
        $set = Query::new()->set(Query::variable('a')->property('b')->replaceWith('c'))->toQuery();

        $this->assertSame('SET a.b = \'c\'', $set);
    }

    public function testPropertyMutation(): void
    {
        $set = Query::new()->set(Query::variable('a')->assign(['a' => 'b'])->setMutate())->toQuery();

        $this->assertSame('SET a += {a: \'b\'}', $set);
    }

    public function testReturnsSameInstance(): void
    {
        $expected = Query::new();
        $actual = $expected->set(Query::variable('a')->assign(['a' => 'b']));

        $this->assertSame($expected, $actual);
    }
}
