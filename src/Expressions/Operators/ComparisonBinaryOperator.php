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
 * - equality: "="
 * - inequality: "<>"
 * - less than: "<"
 * - greater than: ">"
 * - less than or equal to: "<="
 * - greater than or equal to: ">="
 *
 * In additional, there are some string-specific comparison operators:
 *
 * - case-sensitive prefix search on strings: "STARTS WITH"
 * - case-sensitive suffix search on strings: "ENDS WITH"
 * - case-sensitive inclusion search in strings: "CONTAINS"
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison Corresponding documentation on Neo4j.com
 */
abstract class ComparisonBinaryOperator extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @inheritDoc
     *
     * @param AnyType $left  The left-hand of the comparison operator
     * @param AnyType $right The right-hand of the comparison operator
     */
    public function __construct(AnyType $left, AnyType $right)
    {
        parent::__construct($left, $right);
    }
}
