<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Functions\Any;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\Any
 */
class AnyTest extends TestCase
{
	use FunctionTestHelper;

    public function testToQuery()
	{
        $variable = $this->getExpressionMock("variable", $this);
        $list = $this->getExpressionMock("list", $this);
        $predicate = $this->getExpressionMock("predicate", $this);

        $any = new Any($variable, $list, $predicate);

        $this->assertSame("any(variable IN list WHERE predicate)", $any->toQuery());
    }
}