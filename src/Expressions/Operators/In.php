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
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Represents the application of the "IN" operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#where-in-operator Corresponding documentation on Neo4j.com
 */
final class In extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * In constructor.
     *
     * @param PropertyType $left  The left-hand of the expression
     * @param ListType     $right The right-hand of the expression
     */
    public function __construct(PropertyType $left, ListType $right)
    {
        parent::__construct($left, $right);
    }

    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "IN";
    }

    /**
     * @inheritDoc
     */
    protected function getPrecedence(): Precedence
    {
        return Precedence::FUNCTIONAL;
    }
}
