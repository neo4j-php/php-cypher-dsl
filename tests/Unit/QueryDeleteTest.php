<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "delete" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
final class QueryDeleteTest extends TestCase
{
	public function testSingleStructuralType(): void
    {
        $tom = Query::node()->withVariable('tom')->getVariable();

        $query = Query::new()->delete($tom);

        $this->assertSame('DELETE tom', $query->toQuery());
    }

    public function testMultipleStructuralType(): void
    {
        $tom = Query::node()->withVariable('tom')->getVariable();
        $jerry = Query::node()->withVariable('jerry')->getVariable();

        $query = Query::new()->delete([$tom, $jerry]);

        $this->assertSame('DELETE tom, jerry', $query->toQuery());
    }

    public function testSinglePattern(): void
    {
        $tom = Query::node()->withVariable('tom');

        $query = Query::new()->delete($tom);

        $this->assertSame('DELETE tom', $query->toQuery());
    }

    public function testMultiplePatterns(): void
    {
        $tom = Query::node()->withVariable('tom');
        $jerry = Query::node()->withVariable('jerry');

        $query = Query::new()->delete([$tom, $jerry]);

        $this->assertSame('DELETE tom, jerry', $query->toQuery());
    }

    public function testPatternWithoutVariable(): void
    {
        $pattern = Query::node();

        $query = Query::new()->delete($pattern);

        $this->assertStringMatchesFormat('DELETE var%s', $query->toQuery());
    }

    public function testDetachDelete(): void
    {
        $tom = Query::node()->withVariable('tom')->getVariable();

        $query = Query::new()->delete($tom, true);

        $this->assertSame('DETACH DELETE tom', $query->toQuery());
    }

    public function testDetachDeleteDeprecated(): void
    {
        $tom = Query::node()->withVariable('tom')->getVariable();

        $query = Query::new()->detachDelete($tom);

        $this->assertSame('DETACH DELETE tom', $query->toQuery());
    }
}
