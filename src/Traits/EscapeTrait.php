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

use function preg_match;
use function sprintf;
use function str_replace;

/**
 * Trait for encoding certain structures that are used in multiple clauses in a
 * Cypher query.
 */
trait EscapeTrait
{
    /**
     * Escapes a 'name' if it needs to be escaped.
     * @see https://neo4j.com/docs/cypher-manual/4.4/syntax/naming
     * A 'name' in cypher is any string that should be included directly in a cypher query,
     * such as variable names, labels, property names and relation types
     *
     * @param string $name
     * @return string
     */
    private static function escape(string $name): string
    {
        if (preg_match('/^\p{L}[\p{L}\d_]*$/u', $name)) {
            return $name;
        }

        return self::escapeRaw($name);
    }

    /**
     * Escapes the given $name to be used directly in a CYPHER query.
     * Note: according to https://github.com/neo4j/neo4j/issues/12901 backslashes might give problems in some Neo4j versions.
     */
    public static function escapeRaw($name)
    {
        // Escape backticks that are included in $name by doubling them.
        $name = str_replace('`', '``', $name);

        return sprintf("`%s`", $name);
    }
}
