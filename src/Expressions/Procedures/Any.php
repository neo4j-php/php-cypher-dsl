<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Procedures;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents the "any()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-any Corresponding documentation on Neo4j.com
 * @see Procedure::any()
 */
final class Any extends Procedure implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var Variable A variable that can be used from within the predicate
     */
    private Variable $variable;

    /**
     * @var ListType A list
     */
    private ListType $list;

    /**
     * @var AnyType A predicate that is tested against all items in the list
     */
    private AnyType $predicate;

    /**
     * The signature of the "any()" function is "any(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)".
     *
     * @param Variable $variable  A variable that can be used from within the predicate
     * @param ListType $list      A list
     * @param AnyType  $predicate A predicate that is tested against all items in the list
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(Variable $variable, ListType $list, AnyType $predicate)
    {
        $this->variable = $variable;
        $this->list = $list;
        $this->predicate = $predicate;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "any(%s IN %s WHERE %s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->variable, $this->list, $this->predicate];
    }
}
