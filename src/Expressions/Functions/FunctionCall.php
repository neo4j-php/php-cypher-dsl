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

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * This class represents any function call.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/
 */
abstract class FunctionCall extends Expression
{
    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $signature = $this->getSignature();
        $parameters = array_map(
            fn (QueryConvertable $convertable): string => $convertable->toQuery(),
            $this->getParameters()
        );

        return sprintf($signature, ...$parameters);
    }

    /**
     * Returns the signature of this function as a format string. For example for the "all()" function,
     * the signature would be this:
     *
     * "all(%s IN %s WHERE %s)"
     *
     * @return string
     */
    abstract protected function getSignature(): string;

    /**
     * The parameters for this function as QueryConvertable objects. These parameters are inserted, in order, into
     * the signature string retrieved from ::getSignature().
     *
     * @return Expression[]
     */
    abstract protected function getParameters(): array;
}