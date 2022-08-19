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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus
 */
class UnaryMinusTest extends TestCase
{

    public function testToQuery()
    {
        $minus = new UnaryMinus(new Integer(-10));

        $this->assertSame("(- -10)", $minus->toQuery());

        $minus = new UnaryMinus($minus);

        $this->assertSame("(- (- -10))", $minus->toQuery());
    }

    public function testDoesNotAcceptAnyTypeAsOperand()
    {
        $this->expectException(TypeError::class);

        $minus = new UnaryMinus($this->createMock(AnyType::class));

        $minus->toQuery();
    }
}
