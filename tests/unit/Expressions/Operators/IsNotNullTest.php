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
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull
 */
final class IsNotNullTest extends TestCase
{
    public function testToQuery(): void
    {
        $isNotNull = new IsNotNull(new Boolean(true));

        $this->assertSame("true IS NOT NULL", $isNotNull->toQuery());

        $isNotNull = new IsNotNull($isNotNull);

        $this->assertSame("(true IS NOT NULL) IS NOT NULL", $isNotNull->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $isNotNull = new IsNotNull(new Boolean(true));

        $this->assertInstanceOf(BooleanType::class, $isNotNull);
    }
}
