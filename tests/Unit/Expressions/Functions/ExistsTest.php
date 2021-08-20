<?php

namespace Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\Exists;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions\FunctionTestHelper;

class ExistsTest extends \PHPUnit\Framework\TestCase
{
    public function testToQuery() {
        $expression = FunctionTestHelper::getExpressionMock("expression", $this);

        $exists = new Exists($expression);

        $this->assertSame("exists(expression)", $exists->toQuery());
    }
}