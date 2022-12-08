<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\EscapeTrait
 */
final class EscapeTraitTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trait = new class
        {
            use EscapeTrait {
                escape as public;
            }
        };
    }

    /**
     * @dataProvider provideSafeValueIsNotEscapedData
     */
    public function testSafeValueIsNotEscaped(string $expected): void
    {
        $actual = $this->trait->escape($expected);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideUnsafeValueIsEscapedData
     */
    public function testUnsafeValueIsEscaped(string $value): void
    {
        $expected = sprintf("`%s`", $value);
        $actual = $this->trait->escape($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideValueWithBacktickIsProperlyEscapedData
     */
    public function testValueWithBacktickIsProperlyEscaped($input, $expected): void
    {
        $this->assertSame('`foo``bar`', $this->trait->escape("foo`bar"));
    }

    public function testValueWithMoreThan65534CharactersCannotBeEscaped(): void
    {
        $value = str_repeat('a', 65535);

        $this->expectException(InvalidArgumentException::class);

        $this->trait->escape($value);
    }

    public function provideSafeValueIsNotEscapedData(): array
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

    public function provideUnsafeValueIsEscapedData(): array
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

    public function provideValueWithBacktickIsProperlyEscapedData(): array
    {
        return [
            ['foo`bar', '`foo``bar`'],
            ['`foo', '```foo`'],
            ['foo`', '`foo```'],
            ['foo``bar', '`foo````bar`'],
            ['`foo`', '```foo```'],
        ];
    }
}
