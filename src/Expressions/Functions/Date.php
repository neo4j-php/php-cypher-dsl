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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;

/**
 * This class represents the "date()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-date
 * @see Func::date()
 *
 * @internal This class is not covered by the backwards compatibility promise of php-cypher-dsl
 */
final class Date extends Func implements DateType
{
    use DateTypeTrait;

    /**
     * @var AnyType|null The input to the date function, from which to construct the date
     */
    private ?AnyType $value;

    /**
     * The signature of the "date()" function is:
     *
     * date(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATE?)
     *
     * @param AnyType|null $value The input to the date function, from which to construct the date
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
