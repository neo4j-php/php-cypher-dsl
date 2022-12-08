<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Traits;

use InvalidArgumentException;

/**
 * Trait for escaping names.
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
     *
     * @return string The escaped name
     *
     * @see https://neo4j.com/docs/cypher-manual/4.4/syntax/naming Corresponding documentation on Neo4j.com
     */
    private static function escape(string $name): string
    {
        if ($name === "") {
            // Although some versions of Neo4j do not crash when the empty string is used as a name,
            // there is no real reason to ever use the empty string as a name
            throw new InvalidArgumentException("A name cannot be the empty string");
        }

        if (strlen($name) > 65534) {
            // Remark: Some versions of Neo4j support names up to 65535 characters, but we just take the lower bound
            throw new InvalidArgumentException("A name cannot be longer than 65534 (2^16 - 2) characters");
        }

        if (\preg_match('/^\p{L}[\p{L}\d_]*$/u', $name)) {
            // The name is already valid and does not need to be escaped
            return $name;
        }

        // Escape backticks that are included in $name by doubling them.
        $name = \str_replace('`', '``', $name);

        return \sprintf("`%s`", $name);
    }
}
