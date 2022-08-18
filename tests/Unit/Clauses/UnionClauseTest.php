<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021- Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\UnionClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\UnionClause
 */
class UnionClauseTest extends TestCase
{
    public function testNoCombine(): void
    {
        $union = new UnionClause();

        $this->assertEquals('UNION', $union->toQuery());
    }

    public function testAll(): void
    {
        $union = new UnionClause();
        $union->setAll();

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

    public function testCanBeEmpty(): void
    {
        $clause = new UnionClause();

        $this->assertTrue($clause->canBeEmpty());
    }
}
