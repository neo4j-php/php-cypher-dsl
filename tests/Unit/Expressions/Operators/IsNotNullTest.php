<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull
 */
class IsNotNullTest extends TestCase
{

    public function testToQuery(): void
    {
        $isNotNull = new IsNotNull(new Boolean(true), false);

        $this->assertFalse($isNotNull->insertsParentheses());

        $this->assertSame("true IS NOT NULL", $isNotNull->toQuery());

        $isNotNull = new IsNotNull($isNotNull);

        $this->assertSame("(true IS NOT NULL IS NOT NULL)", $isNotNull->toQuery());

        $this->assertTrue($isNotNull->insertsParentheses());
    }
}
