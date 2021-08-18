<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

class NotOperator extends BinaryOperator
{
    protected function getOperator(): string
    {
        return "NOT";
    }
}