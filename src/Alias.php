<?php

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Types\AnyType;
use function sprintf;

class Alias implements AnyType
{
    /**
     * @var Variable The original variable to be aliased
     */
    private Variable $original;

    /**
     * @var Variable The new variable aliasing the original
     */
    private Variable $variable;

    /**
     * BinaryOperator constructor.
     *
     * @param Variable $original The original variable to be aliased
     * @param Variable $variable The new variable aliasing the original
     */
    public function __construct(Variable $original, Variable $variable)
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
     * Gets the original variable of the alias.
     *
     * @return Variable
     */
    public function getOriginal(): Variable
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