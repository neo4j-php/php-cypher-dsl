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

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\FloatTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\IntegerTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalDateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PointTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\StringTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\TimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\FloatType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * This class represents any function call.
 *
 * @see Procedure::raw()
 */
final class Raw extends Procedure implements
    BooleanType,
    DateTimeType,
    DateType,
    FloatType,
    IntegerType,
    ListType,
    LocalDateTimeType,
    LocalTimeType,
    MapType,
    PointType,
    StringType,
    TimeType
{
    use BooleanTypeTrait,
        DateTimeTypeTrait,
        DateTypeTrait,
        FloatTypeTrait,
        IntegerTypeTrait,
        ListTypeTrait,
        LocalDateTimeTypeTrait,
        LocalTimeTypeTrait,
        MapTypeTrait,
        NumeralTypeTrait,
        PointTypeTrait,
        StringTypeTrait,
        TimeTypeTrait;

    use ErrorTrait;
    use EscapeTrait;

    /**
     * @var string The name of the function to call
     */
    private string $functionName;

    /**
     * @var AnyType[] The parameters to pass to the function call
     */
    private array $parameters;

    /**
     * @param string    $functionName The name of the function to call
     * @param AnyType[] $parameters   The parameters to pass to the function call
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(string $functionName, array $parameters)
    {
        self::assertClassArray('parameters', AnyType::class, $parameters);

        $this->functionName = self::escape($functionName);
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        // A string of the form '%s, %s, %s' with count($this->parameters) occurences of '%s'
        $percentSString =
            count($this->parameters) === 0 ?
            '' :
            str_repeat('%s, ', count($this->parameters) - 1) . '%s';

        return $this->functionName . '(' . $percentSString . ')';
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->parameters;
    }
}
