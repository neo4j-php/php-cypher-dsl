<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\IntegerTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;

/**
 * Represents an integer literal.
 */
final class Integer implements IntegerType
{
    use IntegerTypeTrait;

    /**
     * @var int The value
     */
    private int $value;

    /**
     * @param int $value The value
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * Returns the integer value.
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return (string)$this->value;
    }
}
