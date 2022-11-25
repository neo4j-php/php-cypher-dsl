<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions\Literals;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean
 */
final class BooleanTest extends TestCase
{
    public function testTrue(): void
    {
        $boolean = new Boolean(true);

        $this->assertSame("true", $boolean->toQuery());
        $this->assertTrue($boolean->getValue());
    }

    public function testFalse(): void
    {
        $boolean = new Boolean(false);

        $this->assertSame("false", $boolean->toQuery());
        $this->assertFalse($boolean->getValue());
    }

    public function testInstanceOfBooleanType(): void
    {
        $this->assertInstanceOf(BooleanType::class, new Boolean(false));
    }
}
