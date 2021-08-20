<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FunctionTestHelper
{
    public static function getExpressionMock(string $variable, TestCase $testCase): Expression {
        $mock = $testCase->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}