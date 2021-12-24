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

namespace WikibaseSolutions\CypherDSL\Functions;

use WikibaseSolutions\CypherDSL\Traits\DateTimeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;

/**
 * This class represents the "datetime()" function.
 *
 * @note You most likely do not want to use this function directly. You probably want to use the Literal
 * class to construct these objects for you.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime
 */
class DateTime extends FunctionCall implements DateType
{
    use DateTimeTrait;

    /**
     * @var AnyType|null The input to the datetime function, from which to construct the datetime
     */
    private ?AnyType $value;

    /**
     * DateTime constructor. The signature of the "datetime()" function is:
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATETIME?)
     *
     * @param AnyType|null $value The input to the datetime function, from which to construct the datetime
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
        return $this->value ? "datetime(%s)" : "datetime()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}