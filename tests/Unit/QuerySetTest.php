<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use TypeError;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Tests the "set" method of the Query class.
 *
 * @covers \WikibaseSolutions\CypherDSL\Query
 */
class QuerySetTest extends TestCase
{
	public function testSet(): void
	{
		$expression = new PropertyReplacement(new Property(new Variable('a'), 'age'),new Integer(55));

		$statement = (new Query())->set($expression)->build();

		$this->assertSame("SET a.age = 55", $statement);

		$statement = (new Query())->set([$expression, $expression])->build();

		$this->assertSame("SET a.age = 55, a.age = 55", $statement);
	}

	public function testSetRejectsAnyType(): void
	{
        $m = Query::node('Movie');

		$this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
		Query::new()->set([$m, $m]);
	}

	public function testSetWithLabel(): void
	{
		$label = Query::variable("n")->labeled("LABEL1", "LABEL2", "LABEL3");

		$statement = (new Query())->set($label)->build();

		$this->assertSame("SET n:LABEL1:LABEL2:LABEL3", $statement);
	}
}
