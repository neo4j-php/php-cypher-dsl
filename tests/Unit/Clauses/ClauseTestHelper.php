<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Property;

class ClauseTestHelper
{
    public static function getPatternMock(string $toString, TestCase $testCase): Pattern {
        $mock = $testCase->getMockBuilder(Pattern::class)->getMock();
        $mock->method('toQuery')->willReturn($toString);

        return $mock;
    }

    public static function getPropertyMock(string $variable, TestCase $testCase): Property {
        $mock = $testCase->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

    public static function getExpressionMock(string $variable, TestCase $testCase): Expression {
        $mock = $testCase->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}