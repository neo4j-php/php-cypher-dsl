<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;

/**
 * This trait provides a default implementation to satisfy the "BooleanType" interface.
 */
trait BooleanTypeTrait
{
    use CastTrait;
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function and($right, bool $insertParentheses = true): Conjunction
    {
        return new Conjunction($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function or($right, bool $insertParentheses = true): Disjunction
    {
        return new Disjunction($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function xor($right, bool $insertParentheses = true): ExclusiveDisjunction
    {
        return new ExclusiveDisjunction($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function not(): Negation
    {
        return new Negation($this);
    }
}
