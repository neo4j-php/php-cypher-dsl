<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\TypeTraits\CompositeTypeTraits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait
 */
final class ListTypeTraitTest extends TestCase
{
    private PropertyType $a;
    private ListType $list;

    protected function setUp(): void
    {
        $this->a = new Property(new Variable('foo'), 'bar');
        $this->list = new List_;
    }

    public function testHas(): void
    {
        $has = $this->list->has($this->a);

        $this->assertInstanceOf(In::class, $has);
    }
}
