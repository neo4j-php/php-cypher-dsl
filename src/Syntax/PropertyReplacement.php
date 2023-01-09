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

use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents the application of the property replacement (=/+=) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/#set-set-a-property Corresponding documentation on Neo4j.com
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-property-replacement-operator Corresponding documentation on Neo4j.com
 */
final class PropertyReplacement implements QueryConvertible
{
    use ErrorTrait;

    /**
     * @var Property|Variable The name of the property to which we assign a (new) value
     */
    private $property;

    /**
     * @var AnyType The value to assign to the property
     */
    private AnyType $value;

    /**
     * @var bool Whether to use the property mutation (+=) instead of the property replacement (=) operator
     */
    private bool $mutate = false;

    /**
     * PropertyReplacement constructor.
     *
     * @param Property|Variable $property The property or variable to assign a value to
     * @param AnyType           $value    The value to assign to the property
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct($property, AnyType $value)
    {
        self::assertClass('property', [Variable::class, Property::class], $property);

        $this->property = $property;
        $this->value = $value;
    }

    /**
     * Whether to use the property mutation instead of the property replacement
     * operator.
     *
     * @return $this
     */
    public function setMutate(bool $mutate = true): self
    {
        $this->mutate = $mutate;

        return $this;
    }

    /**
     * Returns whether the assignment uses property mutation instead of replacement.
     */
    public function mutates(): bool
    {
        return $this->mutate;
    }

    /**
     * Returns the name of the property to which we assign a (new) value.
     *
     * @return Property|Variable
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Returns value to assign to the property.
     */
    public function getValue(): AnyType
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf(
            "%s %s %s",
            $this->property->toQuery(),
            $this->mutate ? "+=" : "=",
            $this->value->toQuery()
        );
    }
}
