<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\UnionClause;
use WikibaseSolutions\CypherDSL\Query;

class UnionClauseTest extends TestCase
{
    public function testNoCombine(): void
    {
        $union = new UnionClause();

        $this->assertFalse($union->includesAll());

        $this->assertEquals('UNION', $union->toQuery());
    }

    public function testAll(): void
    {
        $union = new UnionClause();
        $union->setAll();

        $this->assertTrue($union->includesAll());

        $this->assertEquals('UNION ALL', $union->toQuery());
    }

    public function testUnionFactory(): void
    {
        $nodeX = Query::node('X')->withVariable('x');
        $nodeY = Query::node('Y')->withVariable('y');

        $left = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = UnionClause::union($left, $right, false);

        $this->assertEquals('MATCH (x:X) RETURN x UNION MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionFactoryAll(): void
    {
        $nodeX = Query::node('X')->withVariable('x');
        $nodeY = Query::node('Y')->withVariable('y');

        $left = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = UnionClause::union($left, $right, true);

        $this->assertEquals('MATCH (x:X) RETURN x UNION ALL MATCH (y:Y) RETURN y', $query->toQuery());
    }
}
