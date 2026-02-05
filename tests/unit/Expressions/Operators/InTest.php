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
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\In
 */
final class InTest extends TestCase
{
    public function testToQuery(): void
    {
        $in = new In(new Property(new Variable('v'), "a"), new Variable('b'));

        $this->assertSame("v.a IN b", $in->toQuery());

        $in = new In($in, new List_([new Boolean(true), new Boolean(false)]));

        $this->assertSame("(v.a IN b) IN [true, false]", $in->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $in = new In(new Property(new Variable('a'), 'a'), new Variable('b'));

        $this->assertInstanceOf(BooleanType::class, $in);
    }
}
