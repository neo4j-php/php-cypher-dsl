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

/**
 * Trait for encoding certain structures that are used in multiple clauses in a
 * Cypher query.
 */
trait EscapeTrait
{
    /**
     * Escapes the given 'name'. A name is an unquoted literal in a Cypher query, such as variables,
     * types or property names.
     *
     * @param  string $name
     * @return string
     */
    private function escape(string $name): string
    {
        if ($name === "") {
            return "";
        }

        if (ctype_alpha($name)) {
            return $name;
        }

        if (strpos($name, '`') !== false) {
            throw new InvalidArgumentException("A name must not contain a backtick (`)");
        }

        return sprintf("`%s`", $name);
    }
}