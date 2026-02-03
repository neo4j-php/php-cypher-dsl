<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

/*
 * This enum represents the possible relationship directions.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
 */
enum Direction
{
    /*
     * For the relation (a)-->(b).
     */
    case RIGHT;

    /*
     * For the relation (a)<--(b).
     */
    case LEFT;

    /*
     * For the relation (a)--(b).
     */
    case UNI;
}
