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
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Represents a string-specific comparison binary operator. These are:.
 *
 * - case-sensitive prefix search on strings: "STARTS WITH"
 * - case-sensitive suffix search on strings: "ENDS WITH"
 * - case-sensitive inclusion search in strings: "CONTAINS"
 * - regular expression: "~=" (not part of openCypher)
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical Corresponding documentation on Neo4j.com
 */
abstract class StringSpecificComparisonBinaryOperator extends ComparisonBinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @inheritDoc
     *
     * @param StringType $left  The left-hand of the comparison operator
     * @param StringType $right The right-hand of the comparison operator
     */
    public function __construct(StringType $left, StringType $right, bool $insertParentheses = true)
    {
        parent::__construct($left, $right, $insertParentheses);
    }
}
