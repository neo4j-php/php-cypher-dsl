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
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith
 */
final class EndsWithTest extends TestCase
{
    public function testToQuery(): void
    {
        $endsWith = new EndsWith(new Variable("a"), new String_("b"));

        $this->assertSame("a ENDS WITH 'b'", $endsWith->toQuery());
    }

    public function testCannotBeNested(): void
    {
        $endsWith = new EndsWith(new Variable("a"), new String_("b"));

        $this->expectException(TypeError::class);

        $endsWith = new EndsWith($endsWith, $endsWith);

        $endsWith->toQuery();
    }

    public function testInstanceOfBooleanType(): void
    {
        $endsWith = new EndsWith(Query::variable('a'), Literal::string('a'));

        $this->assertInstanceOf(BooleanType::class, $endsWith);
    }
}
