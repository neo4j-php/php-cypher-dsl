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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Equality
 */
final class EqualityTest extends TestCase
{
    public function testToQuery(): void
    {
        $equality = new Equality(new Integer(10), new Integer(15));

        $this->assertSame("10 = 15", $equality->toQuery());

        $equality = new Equality($equality, $equality);

        $this->assertSame("(10 = 15) = (10 = 15)", $equality->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $equality = new Equality(Literal::string('foo'), Literal::string('bar'));

        $this->assertInstanceOf(BooleanType::class, $equality);
    }
}
