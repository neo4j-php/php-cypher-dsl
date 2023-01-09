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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\LocalTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalTimeType;

/**
 * This class represents the "localtime()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-localtime Corresponding documentation on Neo4j.com
 * @see Procedure::localtime()
 */
final class LocalTime extends Procedure implements LocalTimeType
{
    use LocalTimeTypeTrait;

    /**
     * @var null|AnyType The input to the localtime function, from which to construct the localtime
     */
    private ?AnyType $value;

    /**
     * The signature of the "localtime()" function is "localtime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (LOCALTIME?)".
     *
     * @param null|AnyType $value The input to the localtime function, from which to construct the localtime
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
        return $this->value ? "localtime(%s)" : "localtime()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}
