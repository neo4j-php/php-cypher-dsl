<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

class AndOperator extends BinaryOperator
{
    protected function getOperator(): string
    {
        return "AND";
    }
}