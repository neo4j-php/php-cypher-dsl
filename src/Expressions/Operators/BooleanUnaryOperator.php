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
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a boolean unary operator. These are:.
 *
 * - negation: "NOT"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 50)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-boolean
 */
abstract class BooleanUnaryOperator extends UnaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @inheritDoc
     *
     * @param BooleanType $expression The unary expression
     */
    public function __construct(BooleanType $expression, bool $insertParentheses = true)
    {
        parent::__construct($expression, $insertParentheses);
    }
}
