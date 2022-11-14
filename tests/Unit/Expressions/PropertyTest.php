<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Property
 */
class PropertyTest extends TestCase
{
    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(MapType $map, string $property, string $expected): void
    {
        $property = new Property($map, $property);

        $this->assertSame($expected, $property->toQuery());
    }

    public function provideToQueryData(): array
    {
        return [
            [new Variable("a"), "a", "a.a"],
            [new Map(["b" => new String_("c")]), "b", "{b: 'c'}.b"],
            [new Variable("b"), "a", "b.a"],
            [new Map([":" => new String_("c")]), ":", "{`:`: 'c'}.`:`"],
        ];
    }
}
