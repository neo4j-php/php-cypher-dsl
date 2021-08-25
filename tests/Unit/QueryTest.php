<?php


namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use WikibaseSolutions\CypherDSL\Query;

class QueryTest extends \PHPUnit\Framework\TestCase
{
    public function testMatch() {
        $m = Query::Node("Movie")->named("m");

        $statement = Query::Match($m)::Returning($m)::Build();

        $this->assertSame($statement, "MATCH (m:Movie) RETURN (m:Movie) ");
    }
}