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

namespace WikibaseSolutions\CypherDSL\Traits;

use InvalidArgumentException;
use function preg_match;
use function sprintf;
use function str_replace;

/**
 * Trait for encoding certain structures that are used in multiple clauses in a Cypher query.
 *
 * @internal This trait is not covered by the backwards compatibility guarantee of php-cypher-dsl
 */
trait EscapeTrait
{
    /**
     * Escapes a 'name' if it needs to be escaped.
     *
     * A 'name' in Cypher is any string that should be included directly in a Cypher query,
     * such as variable names, labels, property names and relation types.
     *
     * @param string $name The name to escape
     * @return string The escaped name
     * @see https://neo4j.com/docs/cypher-manual/4.4/syntax/naming
     */
    private static function escape(string $name): string
    {
        if ($name === "") {
            // Although some versions of Neo4j do not crash when the empty string is used as a name, there is no real
            // reason to ever use the empty string as a name
            throw new InvalidArgumentException("A name cannot be the empty string");
        }

        if (strlen($name) > 65534) {
            // Remark: Some versions of Neo4j support names up to 65535 characters, but we just take the lower bound
            throw new InvalidArgumentException("A name cannot be longer than 65534 (2^16 - 2) characters");
        }

        if (preg_match('/^\p{L}[\p{L}\d_]*$/u', $name)) {
            // The name is already valid and does not need to be escaped
            return $name;
        }

        // Escape backticks that are included in $name by doubling them.
        $name = str_replace('`', '``', $name);

        return sprintf("`%s`", $name);
    }
}
