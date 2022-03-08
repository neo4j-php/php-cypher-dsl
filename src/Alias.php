<?php

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Types\AnyType;
use function sprintf;

class Alias implements AnyType
{
    /**
     * @var QueryConvertable The original item to be aliased
     */
    private QueryConvertable $original;

    /**
     * @var Variable The new variable aliasing the original
     */
    private Variable $variable;

    /**
     * BinaryOperator constructor.
     *
     * @param QueryConvertable $original The original item to be aliased
     * @param Variable $variable The new variable aliasing the original
     */
    public function __construct(QueryConvertable $original, Variable $variable)
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
     * @return QueryConvertable
     */
    public function getOriginal(): QueryConvertable
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