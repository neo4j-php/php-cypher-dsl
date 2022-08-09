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
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a comparison binary operator. These are:
 *
 * - "IS NULL"
 * - "IS NOT NULL"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
abstract class ComparisonUnaryOperator extends UnaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * ComparisonUnaryOperator constructor.
     *
     * @param AnyType $expression The expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        parent::__construct($expression, $insertParentheses);
    }
}
