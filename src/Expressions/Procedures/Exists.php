<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Procedures;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * This class represents the "exists()" function.
 *
 * @note: The "exists()" function is deprecated for Neo4j 4.3 and up. Use "IS NOT NULL" instead.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-exists
 * @see Procedure::exists()
 */
final class Exists extends Procedure implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var AnyType The value to check whether it exists
     */
    private AnyType $expression;

    /**
     * The signature of the "exists()" function is:
     *
     * exists(input :: ANY?) :: (BOOLEAN?)
     *
     * @param AnyType $expression The value to check whether it exists
	 * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "exists(%s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->expression];
    }
}
