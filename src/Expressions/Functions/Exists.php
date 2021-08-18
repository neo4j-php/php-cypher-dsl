<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

class Exists extends FunctionCall
{
    private Expression $expression;

    /**
     * @param Expression $expression
     */
    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }


    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "exists(%e)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->expression];
    }
}