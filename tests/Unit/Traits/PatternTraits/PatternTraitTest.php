<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits\PatternTraits;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait
 */
final class PatternTraitTest extends TestCase
{
    private $stub;

    public function setUp(): void
    {
        $this->stub = $this->getMockForTrait(PatternTrait::class);
    }

    public function testWithVariable(): void
    {
        $this->stub->withVariable("hello");

        $this->assertSame("hello", $this->stub->getVariable()->getName());

        $variable = Query::variable("hello");
        $this->stub->withVariable($variable);

        $this->assertSame($variable, $this->stub->getVariable());
    }

    public function testGetVariableWithoutVariable(): void
    {
        $variable = $this->stub->getVariable();

        $this->assertInstanceOf(Variable::class, $variable);
        $this->assertStringMatchesFormat("var%s", $variable->getName());
    }

    public function testGetVariable(): void
    {
        $variable = Query::variable('a');
        $this->stub->withVariable($variable);

        $this->assertSame($variable, $this->stub->getVariable());
    }

    public function testWithVariableReturnsSameInstance(): void
    {
        $expected = $this->stub;
        $actual = $expected->withVariable('foo');

        $this->assertSame($expected, $actual);
    }

	public function testDoesNotAcceptAnyType(): void
	{
		$this->expectException(TypeError::class);

		$this->stub->withVariable(new \stdClass());
	}

    /**
     * @doesNotPerformAssertions
     */
    public function testImplementsPatternCompletely(): void
    {
        new class implements Pattern {
            use PatternTrait;

            public function toQuery(): string
            {
                return '';
            }
        };
    }
}
