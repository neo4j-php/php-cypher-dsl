<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "call" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryCallTest extends TestCase
{
    public function testWithCallable(): void
    {
        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        });

        $this->assertSame('CALL { MATCH (:x) }', $query->toQuery());
    }

    public function testWithCallableOnlyAcceptsQuery(): void
    {
        $this->expectException(TypeError::class);

        Query::new()->call(function (int $query): void {
        });
    }

    public function testWithQuery(): void
    {
        $query = Query::new()->call(Query::new()->match(Query::node()));

        $this->assertSame('CALL { MATCH () }', $query->toQuery());
    }

    public function testWithEmptyQuery(): void
    {
        $query = Query::new()->call(Query::new());

        $this->assertSame('', $query->toQuery());
    }

    public function testWithEmptyQueryAndVariables(): void
    {
        $query = Query::new()->call(Query::new(), ['a', 'b']);

        $this->assertSame('', $query->toQuery());
    }

    public function testWithVariables(): void
    {
        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, Query::variable('x'));

        $this->assertSame('CALL { WITH x MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, [Query::variable('x')]);

        $this->assertSame('CALL { WITH x MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, [Query::variable('x'), Query::variable('y')]);

        $this->assertSame('CALL { WITH x, y MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, 'x');

        $this->assertSame('CALL { WITH x MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, ['x', 'y']);

        $this->assertSame('CALL { WITH x, y MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, Query::node());

        $this->assertStringMatchesFormat('CALL { WITH var%s MATCH (:x) }', $query->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsSubquery(): void
    {
        $this->expectException(TypeError::class);

        Query::new()->call('something bad');
    }

    public function testDoesNotAcceptAnyTypeAsVariables(): void
    {
        $this->expectException(TypeError::class);

        Query::new()->call(Query::new(), 500);
    }
}
