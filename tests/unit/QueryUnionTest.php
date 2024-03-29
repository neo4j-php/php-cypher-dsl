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
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "union" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryUnionTest extends TestCase
{
    public function testUnionQueryAll(): void
    {
        $nodeX = Query::node('X')->withVariable('x');
        $nodeY = Query::node('Y')->withVariable('y');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = $query->union($right, true);

        $this->assertEquals('MATCH (x:X) RETURN x UNION ALL MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionQuery(): void
    {
        $nodeX = Query::node('X')->withVariable('x');
        $nodeY = Query::node('Y')->withVariable('y');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());
        $right = Query::new()->match($nodeY)->returning($nodeY->getVariable());

        $query = $query->union($right, false);

        $this->assertEquals('MATCH (x:X) RETURN x UNION MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionDecorator(): void
    {
        $nodeX = Query::node('X')->withVariable('x');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());

        $query = $query->union(static function (Query $query): void
        {
            $nodeY = Query::node('Y')->withVariable('y');
            $query->match($nodeY)->returning($nodeY->getVariable());
        });

        $this->assertEquals('MATCH (x:X) RETURN x UNION MATCH (y:Y) RETURN y', $query->toQuery());
    }

    public function testUnionDecoratorAll(): void
    {
        $nodeX = Query::node('X')->withVariable('x');

        $query = Query::new()->match($nodeX)->returning($nodeX->getVariable());

        $query = $query->union(static function (Query $query): void
        {
            $nodeY = Query::node('Y')->withVariable('y');
            $query->match($nodeY)->returning($nodeY->getVariable());
        }, true);

        $this->assertEquals('MATCH (x:X) RETURN x UNION ALL MATCH (y:Y) RETURN y', $query->toQuery());
    }
}
