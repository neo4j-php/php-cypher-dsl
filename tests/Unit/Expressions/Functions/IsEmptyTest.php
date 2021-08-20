<?php

namespace Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\IsEmpty;
use WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions\FunctionTestHelper;

class IsEmptyTest extends \PHPUnit\Framework\TestCase
{
    public function testToQuery() {
        $list = FunctionTestHelper::getExpressionMock("list", $this);

        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(list)", $isEmpty->toQuery());
    }
}