<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Single;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\Single
 */
class SingleTest extends TestCase
{
	use FunctionTestHelper;

    public function testToQuery()
	{
        $variable = $this->getExpressionMock("variable", $this);
        $list = $this->getExpressionMock("list", $this);
        $predicate = $this->getExpressionMock("predicate", $this);

        $single = new Single($variable, $list, $predicate);

        $this->assertSame("single(variable IN list WHERE predicate)", $single->toQuery());
    }
}