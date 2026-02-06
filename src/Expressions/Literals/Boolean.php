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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a boolean (true or false) literal.
 */
final class Boolean implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var bool The value
     */
    private bool $value;

    /**
     * @param bool $value The value
     *
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the boolean value.
     */
    public function getValue(): bool
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return $this->value ? "true" : "false";
    }
}
