<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SetClause
 */
final class SetClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $set = new SetClause();

        $this->assertSame("", $set->toQuery());
    }

    public function testAddSinglePropertyReplacement(): void
    {
        $map = Query::variable('a')->assign(['boo' => 'foo', 'far' => 'bar']);

        $set = new SetClause();
        $set->add($map);

        $this->assertSame("SET a = {boo: 'foo', far: 'bar'}", $set->toQuery());
    }

    public function testAddSingleLabel(): void
    {
        $label = Query::variable('a')->labeled('foo');

        $set = new SetClause();
        $set->add($label);

        $this->assertSame("SET a:foo", $set->toQuery());
    }

    public function testAddMultiplePropertyReplacementSingleCall(): void
    {
        $mapA = Query::variable('a')->assign(['boo' => 'foo', 'far' => 'bar']);
        $mapB = Query::variable('b')->assign(['boo' => 'foo', 'far' => 'bar']);

        $set = new SetClause();
        $set->add($mapA, $mapB);

        $this->assertSame("SET a = {boo: 'foo', far: 'bar'}, b = {boo: 'foo', far: 'bar'}", $set->toQuery());
    }

    public function testAddMultiplePropertyReplacementMultipleCalls(): void
    {
        $mapA = Query::variable('a')->assign(['boo' => 'foo', 'far' => 'bar']);
        $mapB = Query::variable('b')->assign(['boo' => 'foo', 'far' => 'bar']);

        $set = new SetClause();
        $set->add($mapA);
        $set->add($mapB);

        $this->assertSame("SET a = {boo: 'foo', far: 'bar'}, b = {boo: 'foo', far: 'bar'}", $set->toQuery());
    }

    public function testAddMultipleLabelsSingleCall(): void
    {
        $labelA = Query::variable('a')->labeled('foo');
        $labelB = Query::variable('b')->labeled('foo');

        $set = new SetClause();
        $set->add($labelA, $labelB);

        $this->assertSame("SET a:foo, b:foo", $set->toQuery());
    }

    public function testAddMultipleLabelsMultipleCalls(): void
    {
        $labelA = Query::variable('a')->labeled('foo');
        $labelB = Query::variable('b')->labeled('foo');

        $set = new SetClause();
        $set->add($labelA);
        $set->add($labelB);

        $this->assertSame("SET a:foo, b:foo", $set->toQuery());
    }

    public function testAddMultipleMixedSingleCall(): void
    {
        $labelA = Query::variable('a')->labeled('foo');
        $mapB = Query::variable('a')->assign(['boo' => 'foo', 'far' => 'bar']);

        $set = new SetClause();
        $set->add($labelA, $mapB);

        $this->assertSame("SET a:foo, a = {boo: 'foo', far: 'bar'}", $set->toQuery());
    }

    public function testAddMultipleMixedMultipleCalls(): void
    {
        $labelA = Query::variable('a')->labeled('foo');
        $mapB = Query::variable('a')->assign(['boo' => 'foo', 'far' => 'bar']);

        $set = new SetClause();
        $set->add($labelA);
        $set->add($mapB);

        $this->assertSame("SET a:foo, a = {boo: 'foo', far: 'bar'}", $set->toQuery());
    }

    public function testAddReturnsSameInstance(): void
    {
        $expected = new SetClause();
        $actual = $expected->add(Query::variable('a')->assign([]));

        $this->assertSame($expected, $actual);
    }

    public function testGetExpressions(): void
    {
        $set = new SetClause();

        $this->assertSame([], $set->getExpressions());

        $labelA = Query::variable('a')->labeled('foo');

        $set->add($labelA);

        $this->assertSame([$labelA], $set->getExpressions());

        $labelB = Query::variable('b')->labeled('foo');

        $set->add($labelB);

        $this->assertSame([$labelA, $labelB], $set->getExpressions());
    }

    public function testCanBeEmpty(): void
    {
        $clause = new SetClause();

        $this->assertFalse($clause->canBeEmpty());
    }
}
