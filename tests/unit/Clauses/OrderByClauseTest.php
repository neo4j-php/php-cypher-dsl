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
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\OrderByClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\OrderByClause
 */
final class OrderByClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $orderBy = new OrderByClause();

        $this->assertSame("", $orderBy->toQuery());
    }

    public function testSingleProperty(): void
    {
        $property = Query::variable('a')->property('a');

        $orderBy = new OrderByClause();
        $orderBy->addProperty($property);

        $this->assertSame("ORDER BY a.a", $orderBy->toQuery());
    }

    public function testMultipleProperties(): void
    {
        $a = Query::variable('a')->property('a');
        $b = Query::variable('a')->property('b');

        $orderBy = new OrderByClause();

        $orderBy->addProperty($a);
        $orderBy->addProperty($b);

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
    }

    public function testMultiplePropertiesSingleCall(): void
    {
        $a = Query::variable('a')->property('a');
        $b = Query::variable('a')->property('b');

        $orderBy = new OrderByClause();

        $orderBy->addProperty($a, $b);

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
    }

    public function testMultiplePropertiesSingleCallArrayUnpacking(): void
    {
        $a = Query::variable('a')->property('a');
        $b = Query::variable('a')->property('b');

        $orderBy = new OrderByClause();

        $orderBy->addProperty(...[$a, $b]);

        $this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
    }

    public function testSinglePropertyDesc(): void
    {
        $a = Query::variable('a')->property('a');

        $orderBy = new OrderByClause();
        $orderBy->addProperty($a);
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());
    }

    public function testMultiplePropertiesDesc(): void
    {
        $a = Query::variable('a')->property('a');
        $b = Query::variable('a')->property('b');

        $orderBy = new OrderByClause();

        $orderBy->addProperty($a);
        $orderBy->addProperty($b);
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a, a.b DESCENDING", $orderBy->toQuery());
    }

    public function testSetDescendingTakesBoolean(): void
    {
        $a = Query::variable('a')->property('a');

        $orderBy = new OrderByClause();

        $orderBy->addProperty($a);
        $orderBy->setDescending();

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());

        $orderBy->setDescending(true);

        $this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());

        $orderBy->setDescending(false);

        $this->assertSame("ORDER BY a.a", $orderBy->toQuery());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $orderBy = new OrderByClause();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $orderBy->addProperty('a');
    }

    public function testGetProperties(): void
    {
        $orderBy = new OrderByClause();

        $a = Query::variable()->property('a');
        $b = Query::variable()->property('b');

        $orderBy->addProperty($a, $b);

        $this->assertSame([$a, $b], $orderBy->getProperties());
    }

    public function testIsDescending(): void
    {
        $orderBy = new OrderByClause();

        $this->assertFalse($orderBy->isDescending());

        $orderBy->setDescending();

        $this->assertTrue($orderBy->isDescending());
    }

    public function testAddPropertyReturnsSameInstance(): void
    {
        $original = new OrderByClause();
        $returned = $original->addProperty(Query::variable()->property('a'));

        $this->assertSame($original, $returned);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new OrderByClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
