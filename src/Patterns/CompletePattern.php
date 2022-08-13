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

use WikibaseSolutions\CypherDSL\Traits\PatternTraits\CompletePatternTrait;

/**
 * Interface to mark patterns that are complete, i.e. can be matched by a MATCH clause or created by
 * a CREATE clause. These are:
 *
 * - node
 * - path
 *
 * A relationship in itself cannot be matched/created, and therefore does not implement this interface.
 *
 * @see CompletePatternTrait for a default implementation
 */
interface CompletePattern extends Pattern
{
}
