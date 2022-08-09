<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
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
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 108)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/#set-set-a-property
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-property-replacement-operator
 */
final class PropertyReplacement implements QueryConvertible
{
    use ErrorTrait;

    /**
     * @var Variable|Property The property to assign a value to
     */
    private $property;

    /**
     * @var AnyType The value to assign to the property
     */
    private AnyType $value;

    /**
     * @var bool Whether to use the property mutation instead of the property replacement operator
     */
    private bool $mutate = false;

    /**
     * Assignment constructor.
     *
     * @param Variable|Property $property The property or variable to assign a value to
     * @param AnyType $value The value to assign to the property
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
     * @param bool $mutate
     * @return $this
     */
    public function setMutate(bool $mutate = true): self
    {
        $this->mutate = $mutate;

        return $this;
    }

    /**
     * Returns whether the assignment uses property mutation instead of replacement.
     *
     * @return bool
     */
    public function mutates(): bool
    {
        return $this->mutate;
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
