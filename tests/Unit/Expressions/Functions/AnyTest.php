<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\Any;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class AnyTest extends TestCase
{
    public function testToQuery() {
        $variable = FunctionTestHelper::getExpressionMock("variable", $this);
        $list = FunctionTestHelper::getExpressionMock("list", $this);
        $predicate = FunctionTestHelper::getExpressionMock("predicate", $this);

        $any = new Any($variable, $list, $predicate);

        $this->assertSame("any(variable IN list WHERE predicate)", $any->toQuery());
    }
}