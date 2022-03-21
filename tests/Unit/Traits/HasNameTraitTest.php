<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use LogicException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\HasNameTrait;

class HasNameTraitTest extends TestCase
{
    private $hasName;

    public function setUp(): void
    {
        $this->hasName = new class () {
            use HasNameTrait {
                configureName as public;
                generateName as public;
            }
        };
    }

    public function testHasName(): void
    {
        $this->expectException(LogicException::class);

        $this->hasName->getName();
    }

    public function testGenerateName(): void
    {
        $this->assertMatchesRegularExpression('/var\w{32}/', $this->hasName->generateName());
        $this->assertMatchesRegularExpression('/x\w{16}/', $this->hasName->generateName('x', 16));
    }

    public function testConfigureName(): void
    {
        $this->hasName->configureName(null, 'y', 16);

        $this->assertMatchesRegularExpression('/y\w{16}/', $this->hasName->getName());
    }
}
