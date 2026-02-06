<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits\MethodTraits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\MethodTraits\PropertyMethodTrait
 */
final class PropertyMethodTraitTest extends TestCase
{
    private MapType $a;

    protected function setUp(): void
    {
        $this->a = new Map;
    }

    public function testProperty(): void
    {
        $property = $this->a->property("foo");

        $this->assertInstanceOf(Property::class, $property);
    }
}
