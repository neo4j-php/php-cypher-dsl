<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\Exists;

class ExistsTest extends \PHPUnit\Framework\TestCase
{
    public function testToQuery() {
        $expression = FunctionTestHelper::getExpressionMock("expression", $this);

        $exists = new Exists($expression);

        $this->assertSame("exists(expression)", $exists->toQuery());
    }
}