<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\NameGenerationTrait;

class NameGenerationTraitTest extends TestCase
{
    private $hasName;

    public function setUp(): void
    {
        $this->hasName = new class () {
            use NameGenerationTrait {
                generateIdentifier as public;
            }
        };
    }

    public function testGenerateName(): void
    {
        $this->assertMatchesRegularExpression('/var\w{32}/', $this->hasName->generateIdentifier('var'));
        $this->assertMatchesRegularExpression('/x\w{16}/', $this->hasName->generateIdentifier('x', 16));
    }

}
