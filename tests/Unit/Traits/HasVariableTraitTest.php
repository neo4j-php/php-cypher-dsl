<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\HasVariable;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\HasVariableTrait;

class HasVariableTraitTest extends TestCase
{
    private $hasVariable;

    public function setUp(): void
    {
        $this->hasVariable = new class () implements HasVariable {
            use HasVariableTrait;
        };
    }

    public function testDefaultGeneration(): void
    {
        $this->assertFalse($this->hasVariable->hasVariable());
        $this->assertNotNull($this->hasVariable->getVariable());
        $this->assertTrue($this->hasVariable->hasVariable());

        $this->assertMatchesRegularExpression('/var\w{32}/', $this->hasVariable->getVariable()->getName());
    }

    public function testNamed(): void
    {
        $this->hasVariable->named('x');

        $this->assertTrue($this->hasVariable->hasVariable());
        $this->assertEquals('x', $this->hasVariable->getVariable()->getName());
    }
}
