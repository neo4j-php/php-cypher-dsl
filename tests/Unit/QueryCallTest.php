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
    public function testCallClauseWithCallable(): void
    {
        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        });

        $this->assertSame('CALL { MATCH (:x) }', $query->toQuery());
    }

    public function testCallClauseVariables(): void
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
        }, Query::node()->withVariable('x'));

        $this->assertSame('CALL { WITH x MATCH (:x) }', $query->toQuery());

        $query = Query::new()->call(function (Query $query) {
            $query->match(Query::node('x'));
        }, 'x');

        $this->assertSame('CALL { WITH x MATCH (:x) }', $query->toQuery());
    }

    public function testCallClauseDoesNotAcceptAnyTypeAsSubquery(): void
    {
        $this->expectException(TypeError::class);

        Query::new()->call('something bad');
    }

    public function testCallClauseDoesNotAcceptAnyTypeAsVariables(): void
    {
        $this->expectException(TypeError::class);

        Query::new()->call(Query::new(), 500);
    }
}
