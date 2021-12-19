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

namespace WikibaseSolutions\CypherDSL;

use TypeError;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use function get_class;
use function get_debug_type;
use function gettype;
use function is_bool;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;

/**
 * This class represents a list of expressions. For example, this class can represent the following
 * construct:
 *
 * ['a', 2, n.property]
 *
 * @see PropertyMap for a construct that takes keys into account
 */
class ExpressionList implements ListType
{
    use EscapeTrait;
    use ListTypeTrait;

    /**
     * @var array The list of expressions
     */
    private array $expressions;

    /**
     * ExpressionList constructor.
     *
     * @param AnyType[] $expressions The list of expressions, should be homogeneous
     */
    public function __construct(array $expressions)
    {
        foreach ($expressions as $expression) {
            if (!($expression instanceof AnyType)) {
                throw new TypeError("\$expressions must be an array of only AnyType objects");
            }
        }

        $this->expressions = $expressions;
    }

    /**
     * Constructs an expression list from literals (eg. strings, numbers and booleans)
     * @param iterable $literals
     * @return static
     */
    public static function fromLiterals(iterable $literals): self
    {
        $tbr = [];
        foreach ($literals as $literal) {
            if (is_string($literal) || (is_object($literal) && method_exists($literal, '__toString'))) {
                $tbr[] = new StringLiteral((string) $literal);
            } else if (is_bool($literal)) {
                $tbr[] = new Boolean($literal);
            } else if (is_int($literal) || is_float($literal)) {
                $tbr[] = new Decimal($literal);
            } else {
                $actualType = is_object($literal) ? get_class($literal) : gettype($literal);
                throw new TypeError(sprintf('Cannot create a literal from: "%s".', $actualType));
            }
        }

        return new self($tbr);
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $expressions = array_map(
            fn(AnyType $expression): string => $expression->toQuery(),
            $this->expressions
        );

        return sprintf("[%s]", implode(", ", $expressions));
    }
}