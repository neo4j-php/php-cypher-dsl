<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a comparison binary operator. These are:.
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
     * @inheritDoc
     *
     * @param AnyType $expression The unary expression
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        parent::__construct($expression, $insertParentheses);
    }
}
