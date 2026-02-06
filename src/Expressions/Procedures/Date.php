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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;

/**
 * This class represents the "date()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date Corresponding documentation on Neo4j.com
 * @see Procedure::date()
 */
final class Date extends Procedure implements DateType
{
    use DateTypeTrait;

    /**
     * @var null|AnyType The input to the date function, from which to construct the date
     */
    private ?AnyType $value;

    /**
     * The signature of the "date()" function is "date(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATE?)".
     *
     * @param null|AnyType $value The input to the date function, from which to construct the date
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
        return $this->value ? "date(%s)" : "date()";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return $this->value ? [$this->value] : [];
    }
}
