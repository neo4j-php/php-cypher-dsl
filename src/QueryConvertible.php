<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL;

/**
 * This interface represents any class that can be converted into a (partial) Cypher query.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/
 */
interface QueryConvertible
{
    /**
     * Converts the object into a (partial) query.
     *
     * @return string
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function toQuery(): string;
}
