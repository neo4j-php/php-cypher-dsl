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
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull
 */
class IsNullTest extends TestCase
{

    public function testToQuery(): void
    {
        $isNull = new IsNull(new Boolean(true), false);

        $this->assertFalse($isNull->insertsParentheses());

        $this->assertSame("true IS NULL", $isNull->toQuery());

        $isNull = new IsNull($isNull);

        $this->assertSame("(true IS NULL IS NULL)", $isNull->toQuery());

        $this->assertTrue($isNull->insertsParentheses());
    }
}
