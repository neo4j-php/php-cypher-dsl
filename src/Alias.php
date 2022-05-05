<?php

namespace WikibaseSolutions\CypherDSL;

use function sprintf;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents aliasing an expression or variable.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/#with-introduce-variables
 */
class Alias implements AnyType
{
    /**
     * @var AnyType The original item to be aliased
     */
    private AnyType $original;

    /**
     * @var Variable The new variable aliasing the original
     */
    private Variable $variable;

    /**
     * Alias constructor.
     *
     * @param AnyType $original The original item to be aliased
     * @param Variable $variable The new variable aliasing the original
     */
    public function __construct(AnyType $original, Variable $variable)
    {
        $this->original = $original;
        $this->variable = $variable;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s AS %s", $this->original->toQuery(), $this->variable->toQuery());
    }

    /**
     * Gets the original item of the alias.
     *
     * @return AnyType
     */
    public function getOriginal(): AnyType
    {
        return $this->original;
    }

    /**
     * Gets the variable from the alias.
     *
     * @return Variable
     */
    public function getVariable(): Variable
    {
        return $this->variable;
    }
}
