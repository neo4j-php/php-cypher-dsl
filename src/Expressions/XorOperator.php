<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

class XOrOperator extends BinaryOperator
{
    protected function getOperator(): string
    {
        return "XOR";
    }
}