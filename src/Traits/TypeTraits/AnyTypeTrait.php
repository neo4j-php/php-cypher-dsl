<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This trait provides a default implementation to satisfy the "AnyType" interface.
 */
trait AnyTypeTrait
{
    /**
     * @inheritDoc
     */
    public function alias(Variable|string $right): Alias
    {
        return new Alias($this, CastUtils::toName($right));
    }

    /**
     * @inheritDoc
     */
    public function equals(AnyType|Pattern|string|bool|float|int|array $right): Equality
    {
        return new Equality($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function notEquals(AnyType|Pattern|string|bool|float|int|array $right): Inequality
    {
        return new Inequality($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function gt(AnyType|Pattern|string|bool|float|int|array $right): GreaterThan
    {
        return new GreaterThan($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function gte(AnyType|Pattern|string|bool|float|int|array $right): GreaterThanOrEqual
    {
        return new GreaterThanOrEqual($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function lt(AnyType|Pattern|string|bool|float|int|array $right): LessThan
    {
        return new LessThan($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function lte(AnyType|Pattern|string|bool|float|int|array $right): LessThanOrEqual
    {
        return new LessThanOrEqual($this, CastUtils::toAnyType($right));
    }

    /**
     * @inheritDoc
     */
    public function isNull(bool $insertParentheses = true): IsNull
    {
        return new IsNull($this);
    }

    /**
     * @inheritDoc
     */
    public function isNotNull(bool $insertParentheses = true): IsNotNull
    {
        return new IsNotNull($this);
    }
}
