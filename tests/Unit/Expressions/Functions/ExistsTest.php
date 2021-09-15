<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Exists;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\Exists
 */
class ExistsTest extends TestCase
{
	use FunctionTestHelper;

    public function testToQuery()
	{
        $expression = $this->getExpressionMock("expression", $this);

        $exists = new Exists($expression);

        $this->assertSame("exists(expression)", $exists->toQuery());
    }
}