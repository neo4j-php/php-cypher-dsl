<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\RawExpression
 */
class RawExpressionTest extends TestCase
{
    public function testToQuery(): void
    {
        $rawExpression = new RawExpression("foobar(3 > 4)");

        $this->assertSame("foobar(3 > 4)", $rawExpression->toQuery());
    }
}
