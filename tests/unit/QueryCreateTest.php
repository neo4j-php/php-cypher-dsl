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
use TypeError;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

/**
 * Tests the "create" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryCreateTest extends TestCase
{
    public function testCreateSinglePattern(): void
    {
        $pattern = Query::node()->withVariable('hello');

        $query = Query::new()->create($pattern);

        $this->assertSame('CREATE (hello)', $query->toQuery());
    }

    public function testCreateMultiplePatterns(): void
    {
        $hello = Query::node()->withVariable('hello');
        $world = Query::node()->withVariable('world');

        $query = Query::new()->create([$hello, $world]);

        $this->assertSame('CREATE (hello), (world)', $query->toQuery());
    }

    public function testDoesNotAcceptRelationship(): void
    {
        $rel = Query::relationship(Direction::RIGHT);

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        Query::new()->create($rel);
    }
}
