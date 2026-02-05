<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Operators;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull
 */
final class IsNullTest extends TestCase
{
    public function testToQuery(): void
    {
        $isNull = new IsNull(new Boolean(true), false);

        $this->assertSame("true IS NULL", $isNull->toQuery());

        $isNull = new IsNull($isNull);

        $this->assertSame("(true IS NULL) IS NULL", $isNull->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $isNull = new IsNull(new Boolean(true));

        $this->assertInstanceOf(BooleanType::class, $isNull);
    }
}
