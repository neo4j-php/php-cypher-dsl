<?php

namespace WikibaseSolutions\CypherDSL\Patterns;

/**
 * This enum represents the possible relationship directions.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
 */
enum Direction
{
    /**
     * For the relation (a)-->(b).
     */
    case RIGHT;

    /**
     * For the relation (a)<--(b).
     */
    case LEFT;

    /**
     * For the relation (a)--(b).
     */
    case UNI;
}
