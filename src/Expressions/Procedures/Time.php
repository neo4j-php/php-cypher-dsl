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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\TimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;

/**
 * This class represents the "time()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-time
 * @see Procedure::time()
 */
final class Time extends Procedure implements TimeType
{
    use TimeTypeTrait;

    /**
     * @var null|AnyType The input to the localtime function, from which to construct the time
     */
    private ?AnyType $value;

    /**
     * The signature of the "time()" function is:.
     *
     * time(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (TIME?)
     *
     * @param null|AnyType $value The input to the time function, from which to construct the time
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(?AnyType $value = null)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return $this->value ? "time(%s)" : "time()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}
