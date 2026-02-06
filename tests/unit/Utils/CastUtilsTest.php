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

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * @covers \WikibaseSolutions\CypherDSL\Utils\CastUtils
 */
class CastUtilsTest extends TestCase
{
    public function testToListType(): void
    {
        $list = CastUtils::toListType(['a', 'b', 'c']);

        $this->assertInstanceOf(ListType::class, $list);

        $list = Literal::list(['a', 'b', 'c']);
        $list = CastUtils::toListType($list);

        $this->assertInstanceOf(ListType::class, $list);
    }

    public function testToMapType(): void
    {
        $map = CastUtils::toMapType(['a' => 'a', 'b' => 'b', 'c' => 'c']);

        $this->assertInstanceOf(MapType::class, $map);

        $map = Literal::map(['a' => 'a', 'b' => 'b', 'c' => 'c']);
        $map = CastUtils::toMapType($map);

        $this->assertInstanceOf(MapType::class, $map);
    }

    public function testToStringType(): void
    {
        $string = CastUtils::toStringType('hello');

        $this->assertInstanceOf(StringType::class, $string);

        $string = Literal::string('a');
        $string = CastUtils::toStringType($string);

        $this->assertInstanceOf(StringType::class, $string);
    }

    public function testToNumeralType(): void
    {
        $numeral = CastUtils::toNumeralType(1);

        $this->assertInstanceOf(IntegerType::class, $numeral);

        $numeral = CastUtils::toNumeralType(1.1);

        $this->assertInstanceOf(FloatType::class, $numeral);

        $numeral = Literal::number(1.1);
        $numeral = CastUtils::toNumeralType($numeral);

        $this->assertInstanceOf(FloatType::class, $numeral);

        $numeral = Literal::number(1);
        $numeral = CastUtils::toNumeralType($numeral);

        $this->assertInstanceOf(IntegerType::class, $numeral);
    }

    public function testToIntegerType(): void
    {
        $integer = CastUtils::toIntegerType(10);

        $this->assertInstanceOf(IntegerType::class, $integer);

        $integer = Literal::integer(10);
        $integer = CastUtils::toIntegerType($integer);

        $this->assertInstanceOf(IntegerType::class, $integer);
    }

    public function testToBooleanType(): void
    {
        $boolean = CastUtils::toBooleanType(true);

        $this->assertInstanceOf(BooleanType::class, $boolean);

        $boolean = Literal::boolean(true);
        $boolean = CastUtils::toBooleanType($boolean);

        $this->assertInstanceOf(BooleanType::class, $boolean);
    }

    public function testToPropertyType(): void
    {
        $property = CastUtils::toPropertyType('test');

        $this->assertInstanceOf(StringType::class, $property);

        $property = CastUtils::toPropertyType(true);

        $this->assertInstanceOf(BooleanType::class, $property);

        $property = Literal::boolean(true);
        $property = CastUtils::toBooleanType($property);

        $this->assertInstanceOf(BooleanType::class, $property);
    }

    public function testToStructuralType(): void
    {
        $structural = CastUtils::toStructuralType(Query::node());

        $this->assertInstanceOf(StructuralType::class, $structural);

        $structural = CastUtils::toStructuralType(Query::node()->getVariable());

        $this->assertInstanceOf(StructuralType::class, $structural);
    }

    public function testToVariable(): void
    {
        $variable = CastUtils::toVariable('a');

        $this->assertInstanceOf(Variable::class, $variable);

        $variable = CastUtils::toVariable(Query::node());

        $this->assertInstanceOf(Variable::class, $variable);

        $variable = CastUtils::toVariable(new Variable('a'));

        $this->assertInstanceOf(Variable::class, $variable);
    }

    public function testToName(): void
    {
        $name = CastUtils::toName('a');

        $this->assertInstanceOf(Variable::class, $name);

        $name = CastUtils::toName(new Variable('a'));

        $this->assertInstanceOf(Variable::class, $name);
    }
}
