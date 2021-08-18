<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

class None extends FunctionCall
{
    private Expression $variable;
    private Expression $list;
    private Expression $predicate;

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        // TODO: Implement getSignature() method.
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        // TODO: Implement getParameters() method.
    }
}