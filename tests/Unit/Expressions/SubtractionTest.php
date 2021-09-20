<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Subtraction;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Subtraction
 */
class SubtractionTest extends TestCase
{
    public function testToQuery()
    {
        $subtraction = new Subtraction($this->getExpressionMock("a"), $this->getExpressionMock("b"));

        $this->assertSame("(a - b)", $subtraction->toQuery());

        $subtraction = new Subtraction($subtraction, $subtraction);

        $this->assertSame("((a - b) - (a - b))", $subtraction->toQuery());
    }

    /**
     * Returns a mock of the Expression class that returns the given string when toQuery() is called.
     *
     * @param  string $variable
     * @return Expression|MockObject
     */
    private function getExpressionMock(string $variable): Expression
    {
        $mock = $this->getMockBuilder(Expression::class)->getMock();
        $mock->method('toQuery')->willReturn($variable);

        return $mock;
    }
}