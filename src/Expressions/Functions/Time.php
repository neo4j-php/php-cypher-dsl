<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\TimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * This class represents the "time()" function.
 *
 * @note You most likely do not want to use this function directly. You probably want to use the Literal
 * class to construct these objects for you.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time
 */
class Time extends FunctionCall implements TimeType
{
    use TimeTypeTrait;

    /**
     * @var AnyType|null The input to the localtime function, from which to construct the time
     */
    private ?AnyType $value;

    /**
     * Time constructor. The signature of the "time()" function is:
     *
     * time(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (TIME?)
     *
     * @param AnyType|null $value The input to the time function, from which to construct the time
     */
    public function __construct(?AnyType $value = null)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return $this->value ? "time(%s)" : "time()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}
