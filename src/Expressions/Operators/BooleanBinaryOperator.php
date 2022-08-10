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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a boolean binary operator. These are:
 *
 * - conjunction: "AND"
 * - disjunction: "OR"
 * - exclusive disjunction: "XOR"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 50)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean
 */
abstract class BooleanBinaryOperator extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * BooleanBinaryOperator constructor.
     *
     * @param BooleanType $left The left-hand of the boolean operator
     * @param BooleanType $right The right-hand of the boolean operator
     * @param bool $insertParentheses Whether to insert parentheses around the expression
	 * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(BooleanType $left, BooleanType $right, bool $insertParentheses = true)
    {
        parent::__construct($left, $right, $insertParentheses);
    }
}