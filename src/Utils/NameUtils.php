<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Utils;

use InvalidArgumentException;

/**
 * Utility functions for names.
 *
 * @internal This class is not covered by the backwards compatibility guarantee of php-cypher-dsl
 */
class NameUtils
{
    /**
     * Hashtable of generated identifiers.
     *
     * @var array<string, string>
     */
    private static array $generatedIdentifiers = [];

    /**
     * Generates a guaranteed unique random identifier.
     *
     * @note The returned string is not cryptographically secure.
     *
     * @param string $prefix The prefix to put before the name. Must start with a letter.
     * @param int    $length the length of the generated name in bytes
     */
    public static function generateIdentifier(string $prefix = 'var', int $length = 32): string
    {
        do {
            $random = $prefix;

            for ($i = 0; $i < $length; ++$i) {
                $random .= dechex(mt_rand(0, 15));
            }
        } while (isset(self::$generatedIdentifiers[$random]));

        self::$generatedIdentifiers[$random] = '';

        return $random;
    }

    /**
     * Escapes a name.
     *
     * A name in Cypher is any string that should be included directly in a Cypher query,
     * such as variable names, labels, property names and relation types.
     *
     * @see https://neo4j.com/docs/cypher-manual/4.4/syntax/naming Corresponding documentation on Neo4j.com
     */
    public static function escape(string $name): string
    {
        if ($name === "") {
            // Although some versions of Neo4j do not crash when the empty string is used as a name,
            // but there is no real reason to ever use the empty string as a name, so we disallow it.
            throw new InvalidArgumentException("A name cannot be the empty string");
        }

        if (\strlen($name) > 65534) {
            // Remark: Some versions of Neo4j support names up to 65535 characters, but we just take
            // the lower bound. The user shouldn't be using names this long anyway.
            throw new InvalidArgumentException("A name cannot be longer than 65534 (2^16 - 2) characters");
        }

        if (\preg_match('/^\p{L}[\p{L}\d_]*$/u', $name)) {
            // The name is already valid and does not need to be escaped.
            return $name;
        }

        // Escape backticks that are included in the name by doubling them.
        $name = \str_replace('`', '``', $name);

        return \sprintf("`%s`", $name);
    }
}
