<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "match" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QueryMatchTest extends TestCase
{
    public function testMatch(): void
    {
        $m = Query::node('Movie')->withVariable('m');

        $statement = Query::new()->match($m)->build();

        $this->assertSame("MATCH (m:Movie)", $statement);

        $statement = Query::new()->match([$m, $m])->build();

        $this->assertSame("MATCH (m:Movie), (m:Movie)", $statement);
    }

    public function testMatchDoesNotAcceptRelationship(): void
    {
        $r = Query::relationship(Relationship::DIR_LEFT);

        $this->expectException(TypeError::class);

        Query::new()->match($r);
    }

    public function testMatchDoesNotAcceptRelationshipWithNode(): void
    {
        $r = Query::relationship(Relationship::DIR_LEFT);
        $m = Query::node();

        $this->expectException(TypeError::class);

        Query::new()->match([$m, $r]);
    }

	public function testMatchDoesNotAcceptTypeOtherThanMatchablePattern(): void
	{
		$this->expectException(TypeError::class);

		Query::new()->match(false);
	}
}
