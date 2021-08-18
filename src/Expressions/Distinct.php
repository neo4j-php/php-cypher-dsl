<?php

namespace WikibaseSolutions\CypherDSL\Expressions;

class Distinct implements Expression
{

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return "DISTINCT";
    }
}