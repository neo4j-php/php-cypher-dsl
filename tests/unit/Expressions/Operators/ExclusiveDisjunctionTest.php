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
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction
 */
final class ExclusiveDisjunctionTest extends TestCase
{
    public function testToQuery(): void
    {
        $xor = new ExclusiveDisjunction(new Boolean(true), new Boolean(false));

        $this->assertSame("true XOR false", $xor->toQuery());

        $xor = new ExclusiveDisjunction($xor, $xor);

        $this->assertSame("(true XOR false) XOR (true XOR false)", $xor->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $and = new ExclusiveDisjunction(new Boolean(true), new Boolean(false));

        $this->assertInstanceOf(BooleanType::class, $and);
    }
}
