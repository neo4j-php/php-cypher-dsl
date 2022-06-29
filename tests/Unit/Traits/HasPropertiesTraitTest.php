<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\HasProperties;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\HasPropertiesTrait;

class HasPropertiesTraitTest extends TestCase
{
    private $propertyTrait;

    public function setUp(): void
    {
        $this->propertyTrait = new class () implements HasProperties {
            use HasPropertiesTrait;

            public function toQuery(): string
            {
                return '';
            }
        };
    }

    public function testGetProperties(): void
    {
        self::assertNull($this->propertyTrait->getProperties());
    }

    public function testWithPropertiesArray(): void
    {
        $this->propertyTrait->withProperties(['x' => Query::literal('y')]);

        self::assertEquals(
            new PropertyMap(['x' => Query::literal('y')]),
            $this->propertyTrait->getProperties()
        );
    }

    public function testWithPropertiesMap(): void
    {
        $this->propertyTrait->withProperties(new PropertyMap(['x' => Query::literal('y')]));

        self::assertEquals(
            new PropertyMap(['x' => Query::literal('y')]),
            $this->propertyTrait->getProperties()
        );
    }

    public function testWithProperty(): void
    {
        $this->propertyTrait->withProperty('x', Query::literal('y'));
        $this->propertyTrait->withProperty('z', Query::literal('z'));

        self::assertEquals(
            new PropertyMap(['x' => Query::literal('y'), 'z' => Query::literal('z')]),
            $this->propertyTrait->getProperties()
        );
    }
}
