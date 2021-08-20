<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\None;

class NoneTest extends \PHPUnit\Framework\TestCase
{
    public function testToQuery() {
        $variable = FunctionTestHelper::getExpressionMock("variable", $this);
        $list = FunctionTestHelper::getExpressionMock("list", $this);
        $predicate = FunctionTestHelper::getExpressionMock("predicate", $this);

        $none = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN list WHERE predicate)", $none->toQuery());
    }
}