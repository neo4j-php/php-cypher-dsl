<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Boolean
 */
class BooleanTest extends TestCase
{
    public function testTrue()
    {
        $boolean = new Boolean(true);

        $this->assertSame("true", $boolean->toQuery());
    }

    public function testFalse()
    {
        $boolean = new Boolean(false);

        $this->assertSame("false", $boolean->toQuery());
    }
}