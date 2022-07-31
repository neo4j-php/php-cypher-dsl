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

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * This class represents any function call.
 */
class RawFunction extends FunctionCall implements
	BooleanType,
	ListType,
    MapType,
    NumeralType,
    StringType
{
    use BooleanTypeTrait;
    use ListTypeTrait;
    use MapTypeTrait;
    use NumeralTypeTrait;
    use StringTypeTrait;

    use ErrorTrait;

    /**
     * @var string $functionName The name of the function to call
     */
    private string $functionName;

    /**
     * @var AnyType[] $parameters The parameters to pass to the function call
     */
    private array $parameters;

    /**
     * RawFunction constructor.
     *
     * @param string $functionName The name of the function to call
     * @param AnyType[] $parameters The parameters to pass to the function call
     */
    public function __construct(string $functionName, array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->assertClass('parameter', AnyType::class, $parameter);
        }

        if (!preg_match("/^[a-zA-Z0-9_]+$/", $functionName)) {
            throw new InvalidArgumentException(
                "\$functionName should only consist of alphanumeric characters and underscores"
            );
        }

        $this->functionName = $functionName;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return sprintf(
            "%s(%s)",
            $this->functionName,
            implode(", ", array_fill(0, count($this->parameters), "%s"))
        );
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->parameters;
    }
}
