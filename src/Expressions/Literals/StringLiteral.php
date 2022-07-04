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

namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Represents a string literal.
 *
 * @note The name of this class diverges from the naming scheme, because a class in PHP cannot be named "String".
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/expressions/#cypher-expressions-string-literals
 */
class StringLiteral implements StringType
{
    use StringTypeTrait;

    /**
     * @var string
     */
    private string $value;

    /**
     * @var bool Whether to use double quotes or not.
     */
    private bool $useDoubleQuotes = false;

    /**
     * StringLiteral constructor.
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the string value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns whether the string uses double quotes. Single quotes are used if false.
     *
     * @return bool
     */
    public function usesDoubleQuotes(): bool
    {
        return $this->useDoubleQuotes;
    }

    /**
     * Whether to use double quotes or not.
     *
     * @param bool $useDoubleQuotes
     */
    public function useDoubleQuotes(bool $useDoubleQuotes = true): void
    {
        $this->useDoubleQuotes = $useDoubleQuotes;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        // Encodes both "'" and '"', so returning in either context is safe
        $value = $this->encodeSpecials($this->value);

        if ($this->useDoubleQuotes) {
            return sprintf('"%s"', $value);
        }

        return sprintf("'%s'", $value);
    }

    /**
     * Encodes special string characters in Cypher.
     *
     * @param string $value The string to encode
     * @return string The encoded string
     */
    private static function encodeSpecials(string $value): string
    {
        // See https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (Note on string literals)
        return str_replace(
            ["\\", "\t", "\u{0008}", "\n", "\r", "\f", "'", "\""],
            ["\\\\", "\\t", "\\b", "\\n", "\\r", "\\f", "\\'", "\\\""],
            $value
        );
    }
}
