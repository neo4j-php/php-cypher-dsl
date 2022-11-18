<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;

/**
 * This trait provides a default implementation to satisfy the "ListType" interface.
 */
trait ListTypeTrait
{
    use CastTrait;
    use CompositeTypeTrait;

    /**
     * @inheritDoc
     */
    public function has($left, bool $insertParentheses = true): In
    {
        return new In(self::toPropertyType($left), $this, $insertParentheses);
    }
}
