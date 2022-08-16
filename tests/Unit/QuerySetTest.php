<?php

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

		$this->assertSame("SET a.age", $statement);

		$statement = (new Query())->set([$expression, $expression])->build();

		$this->assertSame("SET a.age, a.age", $statement);
	}

	public function testSetRejectsAnyType(): void
	{
		$m = $this->createMock(AnyType::class);

		$this->expectException(TypeError::class);

		(new Query())->set([$m, $m]);
	}

	public function testSetWithLabel(): void
	{
		$label = Query::variable("n")->labeled("LABEL1", "LABEL2", "LABEL3");

		$statement = (new Query())->set($label)->build();

		$this->assertSame("SET n:LABEL1:LABEL2:LABEL3", $statement);
	}
}
