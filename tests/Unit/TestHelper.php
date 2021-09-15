<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Property;

trait ClauseTestHelper
{
	/**
	 * @param string $toString
	 * @param TestCase $testCase
	 * @return Pattern|MockObject
	 */
    public function getPatternMock(string $toString, TestCase $testCase): Pattern {
        $mock = $testCase->getMockBuilder(Pattern::class)->getMock();
        $mock->method('toQuery')->willReturn($toString);

        return $mock;
    }

	/**
	 * @param string $variable
	 * @param TestCase $testCase
	 * @return Property|MockObject
	 */
    public function getPropertyMock(string $variable, TestCase $testCase): Property {
        $mock = $testCase->getMockBuilder(Property::class)->disableOriginalConstructor()->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }

	/**
	 * @param string $variable
	 * @param TestCase $testCase
	 * @return Expression|MockObject
	 */
    public function getExpressionMock(string $variable, TestCase $testCase): Expression {
        $mock = $testCase->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}