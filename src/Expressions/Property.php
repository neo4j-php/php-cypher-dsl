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

namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\EscapeTrait;

/**
 * Represents a property. A property in Cypher would be something like "n.prop" or "n.a.b".
 */
class Property extends Expression
{
    use EscapeTrait;

    /**
     * @var Expression The expression to which this property belongs
     */
    private Expression $expression;

    /**
     * @var string The name of the property
     */
    private string $property;

    /**
     * Property constructor.
     *
     * @param Expression $expression
     * @param string     $property
     */
    public function __construct(Expression $expression, string $property)
    {
        $this->expression = $expression;
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s.%s", $this->expression->toQuery(), $this->escape($this->property));
    }
}