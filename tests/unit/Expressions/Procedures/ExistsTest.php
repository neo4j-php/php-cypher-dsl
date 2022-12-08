<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Procedures;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Procedures\Exists
 */
final class ExistsTest extends TestCase
{
    public function testToQuery(): void
    {
        $expression = Query::string("expression");
        $exists = new Exists($expression);

        $this->assertSame("exists('expression')", $exists->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $expression = Query::string("expression");
        $exists = new Exists($expression);

        $this->assertInstanceOf(BooleanType::class, $exists);
    }
}
