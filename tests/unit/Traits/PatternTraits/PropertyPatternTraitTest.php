<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\PatternTraits;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Patterns\PropertyPattern;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait
 */
final class PropertyPatternTraitTest extends TestCase
{
    private $stub;

    protected function setUp(): void
    {
        $this->stub = $this->getMockForTrait(PropertyPatternTrait::class);
    }

    public function testProperty(): void
    {
        $this->stub->withVariable("hello");

        $this->assertSame("hello.world", $this->stub->property("world")->toQuery());
    }

    public function testWithProperties(): void
    {
        $properties = Query::map(['a' => 'b']);
        $this->stub->withProperties($properties);

        $this->assertSame($properties, $this->stub->getProperties());
    }

    public function testWithPropertiesLiteral(): void
    {
        $this->stub->withProperties(['a' => 'b']);

        $this->assertSame('{a: \'b\'}', $this->stub->getProperties()->toQuery());
    }

    public function testAddProperty(): void
    {
        $this->stub->withProperties(['a' => 'b']);
        $this->stub->addProperty('hello', 'world');

        $this->assertSame('{a: \'b\', hello: \'world\'}', $this->stub->getProperties()->toQuery());
    }

    public function testAddPropertyWithoutMap(): void
    {
        $this->stub->addProperty('a', 'b');

        $this->assertSame('{a: \'b\'}', $this->stub->getProperties()->toQuery());
    }

    public function testAddPropertyToNotMapThrowsException(): void
    {
        $this->stub->withProperties(Query::variable('foobar'));

        $this->expectException(TypeError::class);

        $this->stub->addProperty('hello', 'world');
    }

    public function testAddPropertyReturnsSameInstance(): void
    {
        $actual = $this->stub->addProperty('hello', 'world');

        $this->assertSame($actual, $this->stub);
    }

    public function testAddProperties(): void
    {
        $this->stub->withProperties(['a' => 'b']);
        $this->stub->addProperties(['c' => 'd', 'e' => 'f']);

        $this->assertSame('{a: \'b\', c: \'d\', e: \'f\'}', $this->stub->getProperties()->toQuery());
    }

    public function testAddPropertiesWithoutMap(): void
    {
        $this->stub->addProperties(['a' => 'b']);

        $this->assertSame('{a: \'b\'}', $this->stub->getProperties()->toQuery());
    }

    public function testAddPropertiesToNotMapThrowsException(): void
    {
        $this->stub->withProperties(Query::variable('foo'));

        $this->expectException(TypeError::class);

        $this->stub->addProperties(['c' => 'd', 'e' => 'f']);
    }

    public function testAddPropertiesReturnsSameInstance(): void
    {
        $actual = $this->stub->withProperties(['a' => 'b']);

        $this->assertSame($this->stub, $actual);
    }

    public function testAddPropertiesDoesNotAcceptMapType(): void
    {
        $this->expectException(TypeError::class);

        $this->stub->addProperties(Query::variable('foobar'));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testImplementsPatternCompletely(): void
    {
        new class implements PropertyPattern
        {
            use PropertyPatternTrait;

            public function toQuery(): string
            {
                return '';
            }
        };
    }
}
