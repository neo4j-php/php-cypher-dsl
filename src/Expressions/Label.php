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
 * Represents a label. A label in Cypher would be something like "n:German" or "n:German:Swedish".
 */
class Label implements Expression
{
	use EscapeTrait;

	/**
	 * @var Expression The expression to which this label belongs
	 */
	private Expression $expression;

	/**
	 * @var string The name of the label
	 */
	private string $label;

	/**
	 * Label constructor.
	 *
	 * @param Expression $expression
	 * @param string $label
	 */
	public function __construct(Expression $expression, string $label)
	{
		$this->expression = $expression;
		$this->label = $label;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		return sprintf("%s:%s", $this->expression->toQuery(), $this->escape($this->label));
	}
}