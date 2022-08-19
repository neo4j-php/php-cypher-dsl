<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists
 */
class ExistsTest extends TestCase
{

    public function testToQuery()
    {
        $expression = $this->createMock(AnyType::class);
        $expression->method('toQuery')->willReturn('expression');

        $exists = new Exists($expression);

        $this->assertSame("exists(expression)", $exists->toQuery());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAcceptsAnyType()
    {
        $expression = $this->createMock(AnyType::class);

        $exists = new Exists($expression);

        $exists->toQuery();
    }
}
