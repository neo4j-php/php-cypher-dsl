<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Utils;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Utils\NameUtils;

/**
 * @covers \WikibaseSolutions\CypherDSL\Utils\NameUtils
 */
class NameUtilsTest extends TestCase
{
    public static function provideSafeValueIsNotEscapedData(): array
    {
        return [
            ['foobar'],
            ['fooBar'],
            ['FOOBAR'],
            ['foo_bar'],
            ['FOO_BAR'],
            ['aaa'],
            ['aaa100'],
            ['a0'],
            ['z10'],
            ['z99'],
            ['ça'],
            ['日'],
        ];
    }

    public static function provideUnsafeValueIsEscapedData(): array
    {
        return [
            ['__FooBar__'],
            ['_'],
            ['__'],
            ['\''],
            ['"'],
            ['0'],
            ['10'],
            ['100'],
            ['1'],
            ['2'],
        ];
    }

    public static function provideValueWithBacktickIsProperlyEscapedData(): array
    {
        return [
            ['foo`bar', '`foo``bar`'],
            ['`foo', '```foo`'],
            ['foo`', '`foo```'],
            ['foo``bar', '`foo````bar`'],
            ['`foo`', '```foo```'],
        ];
    }

    /**
     * @dataProvider provideSafeValueIsNotEscapedData
     */
    public function testSafeValueIsNotEscaped(string $expected): void
    {
        $actual = NameUtils::escape($expected);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideUnsafeValueIsEscapedData
     */
    public function testUnsafeValueIsEscaped(string $value): void
    {
        $expected = sprintf("`%s`", $value);
        $actual = NameUtils::escape($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideValueWithBacktickIsProperlyEscapedData
     */
    public function testValueWithBacktickIsProperlyEscaped($input, $expected): void
    {
        $this->assertSame($expected, NameUtils::escape($input));
    }

    public function testValueWithMoreThan65534CharactersCannotBeEscaped(): void
    {
        $value = str_repeat('a', 65535);

        $this->expectException(InvalidArgumentException::class);

        NameUtils::escape($value);
    }

    public function testGenerateIdentifierWithoutPrefix(): void
    {
        $this->assertMatchesRegularExpression('/^var\w{8}$/', NameUtils::generateIdentifier());
    }

    public function testGenerateIdentifierWithPrefix(): void
    {
        $this->assertMatchesRegularExpression('/^x\w{8}$/', NameUtils::generateIdentifier('x'));
    }

    public function testGenerateIdentifierWithPrefixAndLength(): void
    {
        $this->assertMatchesRegularExpression('/^x\w{16}$/', NameUtils::generateIdentifier('x', 16));
    }
}
