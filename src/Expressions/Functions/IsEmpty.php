<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

class IsEmpty extends FunctionCall
{
    private Expression $list;

    /**
     * @param Expression $list
     */
    public function __construct(Expression $list)
    {
        $this->list = $list;
    }


    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "isEmpty(%s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->list];
    }
}