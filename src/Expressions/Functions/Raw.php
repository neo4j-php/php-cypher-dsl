<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
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
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * This class represents any function call.
 *
 * @see Func::raw()
 *
 * @internal This class is not covered by the backwards compatibility promise of php-cypher-dsl
 */
final class Raw extends Func implements
    BooleanType,
    DateType,
    DateTimeType,
    NumeralType,
    StringType,
    MapType,
    PointType,
    ListType,
    LocalDateTimeType,
    LocalTimeType,
    TimeType
{
    use BooleanTypeTrait,
        DateTypeTrait,
        DateTimeTypeTrait,
        ListTypeTrait,
        LocalDateTimeTypeTrait,
        LocalTimeTypeTrait,
        MapTypeTrait,
        NumeralTypeTrait,
        PointTypeTrait,
        StringTypeTrait,
        TimeTypeTrait;

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
     * @param string $functionName The name of the function to call
     * @param AnyType[] $parameters The parameters to pass to the function call
     */
    public function __construct(string $functionName, array $parameters)
    {
        self::assertClassArray('parameters', AnyType::class, $parameters);
        self::assertValidName($functionName);

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
