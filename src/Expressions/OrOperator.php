<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

class OrOperator extends BinaryOperator
{
    protected function getOperator(): string
    {
        return "OR";
    }
}