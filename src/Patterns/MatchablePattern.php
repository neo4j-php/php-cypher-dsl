<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Traits\PatternTraits\MatchablePatternTrait;

/**
 * Interface to mark patterns that can be matched in a MATCH clause. These are:
 *
 * - node
 * - path
 *
 * A relationship in itself cannot be matched on, and therefore does not implement this interface.
 *
 * @see MatchablePatternTrait for a default implementation
 */
interface MatchablePattern extends Pattern
{
}
