<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\HelperTraits\StringGenerationTrait;

class StringGenerationTraitTest extends TestCase
{
    private $hasName;

    public function setUp(): void
    {
        $this->hasName = new class () {
            use StringGenerationTrait {
                generateString as public;
            }
        };
    }

    public function testGenerateName(): void
    {
        $this->assertMatchesRegularExpression('/var\w{32}/', $this->hasName->generateString('var'));
        $this->assertMatchesRegularExpression('/x\w{16}/', $this->hasName->generateString('x', 16));
    }

}
