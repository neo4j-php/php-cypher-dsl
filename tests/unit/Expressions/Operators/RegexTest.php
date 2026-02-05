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
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\Regex
 */
final class RegexTest extends TestCase
{
    public function testToQuery(): void
    {
        $regex = new Regex(new Variable("a"), new String_("b"));

        $this->assertSame("a =~ 'b'", $regex->toQuery());
    }

    public function testInstanceOfBooleanType(): void
    {
        $regex = new Regex(new Variable("a"), new String_("b"));

        $this->assertInstanceOf(BooleanType::class, $regex);
    }
}
