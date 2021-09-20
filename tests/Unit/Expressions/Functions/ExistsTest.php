<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Exists;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\Exists
 */
class ExistsTest extends TestCase
{
    use TestHelper;

    public function testToQuery()
    {
        $expression = $this->getExpressionMock("expression", $this);

        $exists = new Exists($expression);

        $this->assertSame("exists(expression)", $exists->toQuery());
    }
}