<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * Represents a string literal.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/expressions/#cypher-expressions-string-literals
 */
final class String_ implements StringType
{
    use StringTypeTrait;

    /**
     * @var string The value of the string
     */
    private string $value;

    /**
     * @var bool whether to use double quotes or not
     */
    private bool $useDoubleQuotes = false;

    /**
     * @param string $value The value of the string
     *
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Whether to use double quotes or not.
     */
    public function useDoubleQuotes(bool $useDoubleQuotes = true): void
    {
        $this->useDoubleQuotes = $useDoubleQuotes;
    }

    /**
     * Returns the string value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns whether the string uses double quotes. Single quotes are used if false.
     */
    public function usesDoubleQuotes(): bool
    {
        return $this->useDoubleQuotes;
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
     *
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
