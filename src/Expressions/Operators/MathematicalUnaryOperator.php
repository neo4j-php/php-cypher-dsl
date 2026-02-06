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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * Represents a mathematical unary operator. These are:.
 *
 * - unary minus: "-"
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-mathematical Corresponding documentation on Neo4j.com
 */
abstract class MathematicalUnaryOperator extends UnaryOperator implements FloatType, IntegerType
{
    use NumeralTypeTrait;

    /**
     * @inheritDoc
     *
     * @param NumeralType $expression The unary expression
     */
    public function __construct(NumeralType $expression)
    {
        parent::__construct($expression);
    }
}
