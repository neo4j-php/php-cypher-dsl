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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;

/**
 * This class represents the "datetime()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime Corresponding documentation on Neo4j.com
 * @see Procedure::datetime()
 */
final class DateTime extends Procedure implements DateTimeType
{
    use DateTimeTypeTrait;

    /**
     * @var null|AnyType The input to the datetime function, from which to construct the datetime
     */
    private ?AnyType $value;

    /**
     * The signature of the "datetime()" function is "datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATETIME?)".
     *
     * @param null|AnyType $value The input to the datetime function, from which to construct the datetime
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
        return $this->value ? "datetime(%s)" : "datetime()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}
