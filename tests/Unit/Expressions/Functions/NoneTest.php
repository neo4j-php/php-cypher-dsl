<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Functions\None;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\None
 */
class NoneTest extends TestCase
{
	use FunctionTestHelper;

    public function testToQuery() {
        $variable = $this->getExpressionMock("variable", $this);
        $list = $this->getExpressionMock("list", $this);
        $predicate = $this->getExpressionMock("predicate", $this);

        $none = new None($variable, $list, $predicate);

        $this->assertSame("none(variable IN list WHERE predicate)", $none->toQuery());
    }
}