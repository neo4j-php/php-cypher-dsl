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

use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;

/**
 * This trait provides a default implementation to satisfy the "StringType" interface.
 */
trait StringTypeTrait
{
    use CastTrait;
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function contains($right, bool $insertParentheses = true): Contains
    {
        return new Contains($this, self::toStringType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function endsWith($right, bool $insertParentheses = true): EndsWith
    {
        return new EndsWith($this, self::toStringType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function startsWith($right, bool $insertParentheses = true): StartsWith
    {
        return new StartsWith($this, self::toStringType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function regex($right, bool $insertParentheses = true): Regex
    {
        return new Regex($this, self::toStringType($right), $insertParentheses);
    }
}
