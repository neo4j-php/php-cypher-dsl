<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;

/**
 * Dummy classes
 */
class ErrorHelperDummyA
{
}

;

class ErrorHelperDummyB
{
}

;

class ErrorHelperDummyExtendsA extends ErrorHelperDummyA
{
}

;

class ErrorHelperDummyExtendsB extends ErrorHelperDummyB
{
}

;

/**
 * Tester/Mock class
 */
class ErrorImpl
{
    use ErrorTrait;

    /**
     * Overcome private method problems
     */
    public function call($funcName, $args)
    {
        return call_user_func_array([self::class, $funcName], $args);
    }
}

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\ErrorTrait
 */
final class ErrorTraitTest extends TestCase
{
    protected ErrorImpl $errorImpl;

    public function setUp(): void
    {
        $this->errorImpl = new ErrorImpl();
    }

    /**
     * @doesNotPerformAssertions
     * @dataProvider CorrectAssertionsProvider
     */
    public function testAssertClass($classNames, $userInput): void
    {
        $this->errorImpl->call('assertClass', ['foo', $classNames, $userInput]);
    }

    /**
     * @dataProvider failingAssertionsProvider
     */
    public function testAssertClassFailure($classNames, $userInput): void
    {
        $this->expectException(TypeError::class);
        $this->errorImpl->call('assertClass', ['foo', $classNames, $userInput]);
    }

    public function correctAssertionsProvider(): array
    {
        return [
            [ErrorHelperDummyA::class, new ErrorHelperDummyA()],
            [ErrorHelperDummyA::class, new ErrorHelperDummyExtendsA()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyB::class], new ErrorHelperDummyB()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyB::class], new ErrorHelperDummyExtendsB()],
        ];
    }

    public function failingAssertionsProvider(): array
    {
        return [
            [ErrorHelperDummyA::class, new ErrorHelperDummyB()],
            [ErrorHelperDummyExtendsA::class, new ErrorHelperDummyA()],
            [[ErrorHelperDummyA::class, ErrorHelperDummyExtendsB::class], new ErrorHelperDummyB()],
        ];
    }

    public function testGetTypeErrorText(): void
    {
        $this->assertEquals(
            '$foo should be a WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyA, int given.',
            $this->errorImpl->call('typeError', ['foo', [ErrorHelperDummyA::class], 5])->getMessage()
        );
        $this->assertEquals(
            '$foo should be a ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyA or ' .
            'WikibaseSolutions\CypherDSL\Tests\Unit\Traits\ErrorHelperDummyB, int given.',
            $this->errorImpl->call('typeError', ['foo', [ErrorHelperDummyA::class, ErrorHelperDummyB::class], 5])->getMessage()
        );
    }

    /**
     * @dataProvider getUserInputInfoProvider
     */
    public function testGetUserInputInfo($expected, $input): void
    {
        $this->assertEquals(
            $expected,
            $this->errorImpl->call('getDebugType', [$input])
        );
    }

    public function getUserInputInfoProvider(): array
    {
        return [
            ['string', 'foo'],
            ['int', 5],
            ['float', 3.14],
            ['bool', true],
            ['array', ['foo', 'bar']],
            ['class@anonymous', new class () {
            }],
            [ErrorHelperDummyA::class, new ErrorHelperDummyA()],
        ];
    }
}
