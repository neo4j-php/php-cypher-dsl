<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\DeleteClause
 */
final class DeleteClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $delete = new DeleteClause();

        $this->assertSame("", $delete->toQuery());
        $this->assertEquals([], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testSingleVariable(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);

        $this->assertSame("DELETE a", $delete->toQuery());
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testMultipleVariables(): void
    {
        $delete = new DeleteClause();

        $a = new Variable('a');
        $b = new Variable('b');

        $delete->addStructure($a);
        $delete->addStructure($b);

        $this->assertSame("DELETE a, b", $delete->toQuery());
        $this->assertEquals([$a, $b], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDetachDelete(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);
        $delete->setDetach(true);

        $this->assertSame("DETACH DELETE a", $delete->toQuery());
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertTrue($delete->detachesDeletion());
    }

    public function testAcceptsVariable(): void
    {
        $delete = new DeleteClause();
        $variable = new Variable('a');

        $delete->addStructure($variable);
        $delete->toQuery();
        $this->assertEquals([$variable], $delete->getStructural());
        $this->assertFalse($delete->detachesDeletion());
    }

    public function testDoesNotAcceptAnyType(): void
    {
        $delete = new DeleteClause();
        $variable = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $delete->addStructure($variable);
        $delete->toQuery();
    }

    public function testAddStructure(): void
    {
        $delete = new DeleteClause();

        $variableA = new Variable('a');
        $variableB = new Variable('b');

        $delete->addStructure($variableA, $variableB);

        $this->assertSame("DELETE a, b", $delete->toQuery());
        $this->assertSame([$variableA, $variableB], $delete->getStructural());
    }

    public function testAddStructureDoesNotAcceptAnyType(): void
    {
        $delete = new DeleteClause();

        $variableA = new Variable('a');
        $variableB = $this->createMock(AnyType::class);

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $delete->addStructure($variableA, $variableB);
        $delete->toQuery();
    }

    public function testGetStructural(): void
    {
        $delete = new DeleteClause();

        $this->assertSame([], $delete->getStructural());

        $variable = new Variable('a');
        $delete->addStructure($variable);

        $this->assertSame([$variable], $delete->getStructural());
    }

    public function testDetachesDeletion(): void
    {
        $delete = new DeleteClause();

        $this->assertFalse($delete->detachesDeletion());

        $delete->setDetach();

        $this->assertTrue($delete->detachesDeletion());
    }

    public function testCanBeEmpty(): void
    {
        $clause = new DeleteClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
