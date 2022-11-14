<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Syntax;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents aliasing an expression or variable.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/#with-introduce-variables
 */
final class Alias implements QueryConvertible
{
    /**
     * @var AnyType The original item to be aliased
     */
    private AnyType $original;

    /**
     * @var Variable The new variable aliasing the original
     */
    private Variable $variable;

    /**
     * @param AnyType  $original The original item to be aliased
     * @param Variable $variable The new variable aliasing the original
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $original, Variable $variable)
    {
        $this->original = $original;
        $this->variable = $variable;
    }

    /**
     * Gets the original item of the alias.
     */
    public function getOriginal(): AnyType
    {
        return $this->original;
    }

    /**
     * Gets the variable from the alias.
     */
    public function getVariable(): Variable
    {
        return $this->variable;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return \sprintf("%s AS %s", $this->original->toQuery(), $this->variable->toQuery());
    }
}
