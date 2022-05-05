<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Order;
use WikibaseSolutions\CypherDSL\RawExpression;

class OrderTest extends TestCase
{
    use TestHelper;

    public function testBasicOrder(): void
    {
        $order = new Order($this->getQueryConvertableMock(RawExpression::class, 'x'));

        $this->assertEquals('x', $order->toQuery());
        $this->assertNull($order->getOrdering());
    }

    public function testBasicOrderDescending(): void
    {
        $order = new Order($this->getQueryConvertableMock(RawExpression::class, 'x'), 'desc');

        $this->assertEquals('x DESC', $order->toQuery());
        $this->assertEquals('DESC', $order->getOrdering());
    }

    public function testBasicOrderAscending(): void
    {
        $order = new Order($this->getQueryConvertableMock(RawExpression::class, 'x'), 'asc');

        $this->assertEquals('x ASC', $order->toQuery());
        $this->assertEquals('ASC', $order->getOrdering());
    }

    public function testBasicOrderChange(): void
    {
        $order = new Order($this->getQueryConvertableMock(RawExpression::class, 'x'), 'asc');

        $order->setOrdering(null);

        $this->assertEquals('x', $order->toQuery());
        $this->assertNull($order->getOrdering());
    }

    public function testOrderFalse(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('Ordering must be null, "ASC", "DESC", "ASCENDING" or "DESCENDING"');

        new Order($this->getQueryConvertableMock(RawExpression::class, 'x'), 'ascc');
    }
}
