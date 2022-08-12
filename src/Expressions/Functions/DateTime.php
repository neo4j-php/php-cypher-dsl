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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\DateTimeTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;

/**
 * This class represents the "datetime()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/temporal/#functions-datetime
 * @see Func::datetime()
 */
final class DateTime extends Func implements DateTimeType
{
    use DateTimeTypeTrait;

    /**
     * @var AnyType|null The input to the datetime function, from which to construct the datetime
     */
    private ?AnyType $value;

    /**
     * The signature of the "datetime()" function is:
     *
     * datetime(input = DEFAULT_TEMPORAL_ARGUMENT :: ANY?) :: (DATETIME?)
     *
     * @param AnyType|null $value The input to the datetime function, from which to construct the datetime
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
