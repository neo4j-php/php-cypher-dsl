<?php

namespace WikibaseSolutions\CypherDSL\Tests\Integration;

use Bolt\Bolt;
use Bolt\connection\Socket;
use Bolt\helpers\Auth;
use Exception;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Integration test that executes all queries from ":play movies" onto a live Neo4j database. The point of these tests
 * are to make sure that the syntax is compatible with the latest/installed version of Neo4j.
 */
final class MovieGraphTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $bolt = new Bolt(new Socket());
        $this->protocol = $bolt->build();
    }

    /**
     * @doesNotPerformAssertions
     * @throws Exception
     */
    public function testFindActorNamedTomHanks(): void
    {
        $tom = Query::node()->withVariable('tom')->withProperties([
            'name' => 'Tom Hanks'
        ]);

        $query = Query::new()
            ->match($tom)
            ->returning($tom);

        $this->protocol->run($query);
    }
}