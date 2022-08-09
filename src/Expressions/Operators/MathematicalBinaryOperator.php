<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a mathematical binary operator. These are:
 *
 * - addition: "+"
 * - subtraction: "-"
 * - multiplication: "*"
 * - division: "/"
 * - modulo division: "%"
 * - exponentiation: "^"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical
 */
abstract class MathematicalBinaryOperator extends BinaryOperator implements FloatType, IntegerType
{
    use NumeralTypeTrait;

    /**
     * MathematicalBinaryOperator constructor.
     *
     * @param NumeralType $left The left-hand of the mathematical operator
     * @param NumeralType $right The right-hand of the mathematical operator
     * @param bool $insertParentheses Whether to insert parentheses around the expression
	 * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(NumeralType $left, NumeralType $right, bool $insertParentheses = true)
    {
        parent::__construct($left, $right, $insertParentheses);
    }
}
