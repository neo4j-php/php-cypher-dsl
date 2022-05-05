<?php

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents the IS NOT NULL comparison operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
class IsNotNull implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var AnyType The type to test against null
     */
    private AnyType $expression;
    private bool $insertParentheses;

    /**
     * IS NOT NULL constructor.
     *
     * @param AnyType $expression The type to test against null.
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        $this->expression = $expression;
        $this->insertParentheses = $insertParentheses;
    }

    /**
     * Returns the expression to test against null.
     *
     * @return AnyType
     */
    public function getExpression(): AnyType
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf($this->insertParentheses ? "(%s IS NOT NULL)" : "%s IS NOT NULL", $this->expression->toQuery());
    }

    /**
     * Returns whether or not the operator inserts parenthesis.
     *
     * @return bool
     */
    public function insertsParentheses(): bool
    {
        return $this->insertParentheses;
    }
}
