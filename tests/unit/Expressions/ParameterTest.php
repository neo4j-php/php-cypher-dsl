<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Parameter
 */
class ParameterTest extends TestCase
{
    /**
     * @dataProvider provideToQueryData
     */
    public function testToQuery(string $parameter, string $expected): void
    {
        $parameter = new Parameter($parameter);

        $this->assertSame($expected, $parameter->toQuery());
        $this->assertSame(str_replace('$', '', $expected), $parameter->getParameter());
    }

    /**
     * @dataProvider provideThrowsExceptionOnInvalidData
     */
    public function testThrowsExceptionOnInvalid(string $parameter): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Parameter($parameter);
    }

    public function provideToQueryData(): array
    {
        return [
            ["a", '$a'],
            ["b", '$b'],
            ["foo_bar", '$foo_bar'],
            ["A", '$A'],
        ];
    }

    public function provideThrowsExceptionOnInvalidData(): array
    {
        return [
            [""],
            [str_repeat('a', 65535)],
        ];
    }
}
