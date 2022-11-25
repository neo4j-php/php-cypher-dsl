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

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\CastTrait
 */
final class CastTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->trait = new class
        {
            use CastTrait {
                toListType as public;
                toMapType as public;
                toStringType as public;
                toNumeralType as public;
                toIntegerType as public;
                toBooleanType as public;
                toPropertyType as public;
                toStructuralType as public;
                toVariable as public;
                toName as public;
                toAnyType as public;
            }
        };
    }

    public function testToListType(): void
    {
        $list = $this->trait->toListType(['a', 'b', 'c']);

        $this->assertInstanceOf(ListType::class, $list);

        $list = Literal::list(['a', 'b', 'c']);
        $list = $this->trait->toListType($list);

        $this->assertInstanceOf(ListType::class, $list);
    }

    public function testToMapType(): void
    {
        $map = $this->trait->toMapType(['a' => 'a', 'b' => 'b', 'c' => 'c']);

        $this->assertInstanceOf(MapType::class, $map);

        $map = Literal::map(['a' => 'a', 'b' => 'b', 'c' => 'c']);
        $map = $this->trait->toMapType($map);

        $this->assertInstanceOf(MapType::class, $map);
    }

    public function testToStringType(): void
    {
        $string = $this->trait->toStringType('hello');

        $this->assertInstanceOf(StringType::class, $string);

        $string = Literal::string('a');
        $string = $this->trait->toStringType($string);

        $this->assertInstanceOf(StringType::class, $string);
    }

    public function testToNumeralType(): void
    {
        $numeral = $this->trait->toNumeralType(1);

        $this->assertInstanceOf(IntegerType::class, $numeral);

        $numeral = $this->trait->toNumeralType(1.1);

        $this->assertInstanceOf(FloatType::class, $numeral);

        $numeral = Literal::number(1.1);
        $numeral = $this->trait->toNumeralType($numeral);

        $this->assertInstanceOf(FloatType::class, $numeral);

        $numeral = Literal::number(1);
        $numeral = $this->trait->toNumeralType($numeral);

        $this->assertInstanceOf(IntegerType::class, $numeral);
    }

    public function testToIntegerType(): void
    {
        $integer = $this->trait->toIntegerType(10);

        $this->assertInstanceOf(IntegerType::class, $integer);

        $integer = Literal::integer(10);
        $integer = $this->trait->toIntegerType($integer);

        $this->assertInstanceOf(IntegerType::class, $integer);
    }

    public function testToBooleanType(): void
    {
        $boolean = $this->trait->toBooleanType(true);

        $this->assertInstanceOf(BooleanType::class, $boolean);

        $boolean = Literal::boolean(true);
        $boolean = $this->trait->toBooleanType($boolean);

        $this->assertInstanceOf(BooleanType::class, $boolean);
    }

    public function testToPropertyType(): void
    {
        $property = $this->trait->toPropertyType('test');

        $this->assertInstanceOf(StringType::class, $property);

        $property = $this->trait->toPropertyType(true);

        $this->assertInstanceOf(BooleanType::class, $property);

        $property = Literal::boolean(true);
        $property = $this->trait->toBooleanType($property);

        $this->assertInstanceOf(BooleanType::class, $property);
    }

    public function testToStructuralType(): void
    {
        $structural = $this->trait->toStructuralType(Query::node());

        $this->assertInstanceOf(StructuralType::class, $structural);

        $structural = $this->trait->toStructuralType(Query::node()->getVariable());

        $this->assertInstanceOf(StructuralType::class, $structural);
    }

    public function testToVariable(): void
    {
        $variable = $this->trait->toVariable('a');

        $this->assertInstanceOf(Variable::class, $variable);

        $variable = $this->trait->toVariable(Query::node());

        $this->assertInstanceOf(Variable::class, $variable);

        $variable = $this->trait->toVariable(new Variable('a'));

        $this->assertInstanceOf(Variable::class, $variable);
    }

    public function testToName(): void
    {
        $name = $this->trait->toName('a');

        $this->assertInstanceOf(Variable::class, $name);

        $name = $this->trait->toName(new Variable('a'));

        $this->assertInstanceOf(Variable::class, $name);
    }

    public function testToNameDoesNotAcceptPattern(): void
    {
        $this->expectException(TypeError::class);

        $this->trait->toName(Query::node());
    }
}
