<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Functions\IsEmpty;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Functions\IsEmpty
 */
class IsEmptyTest extends TestCase
{
	use TestHelper;

    public function testToQuery()
	{
        $list = $this->getExpressionMock("list", $this);

        $isEmpty = new IsEmpty($list);

        $this->assertSame("isEmpty(list)", $isEmpty->toQuery());
    }
}