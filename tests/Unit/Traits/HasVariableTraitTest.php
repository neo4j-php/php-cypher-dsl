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
        self::assertNull($this->hasVariable->getVariable());
        self::assertNotNull($this->hasVariable->getVariable());

        self::assertMatchesRegularExpression('/var\w{32}/', $this->hasVariable->getVariable()->getName());
    }

    public function testNamed(): void
    {
        $this->hasVariable->named('x');

        self::assertSame($this->hasVariable->getVariable(), $this->hasVariable->getVariable());
        self::assertEquals('x', $this->hasVariable->getVariable()->getName());
    }
}
