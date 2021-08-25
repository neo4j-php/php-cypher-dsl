<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL;

use phpDocumentor\Reflection\Types\Array_;
use PhpParser\Builder\Class_;
use PhpParser\Node\Expr;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause;
use WikibaseSolutions\CypherDSL\Clauses\OrderByClause;
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Clauses\ReturnClause;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\RelatedNodes;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;
use function PHPUnit\Framework\throwException;

class Query
{
	// TODO: Write this class;
	// This class will contain helper functions as well as provide a structure for
	// constructing multi-clause queries. It will be similar in usage to the "Cypher"
	// class in the Java DSL (https://neo4j-contrib.github.io/cypher-dsl/current/).

    /**
     * @var Clause[] $clauses
     */
    private static array $clauses = [];

    /**
     * Creates a Node
     * @param string $node
     * @return Node
     */
    public static function Node(string $node): Node {
        return new Node($node);
    }

    /**
     * Creates related node pattern
     * @param Pattern $left
     * @param Pattern $right
     * @param array $direction
     * @return RelatedNodes
     */
    public static function RelatedNode(Pattern $left, Pattern $right, array $direction): RelatedNodes {
        return new RelatedNodes($left, $right, $direction);
    }

    /**
     * Creates a relationship
     * @param Pattern $a
     * @param Pattern $b
     * @param array $direction
     * @return Relationship
     */
    public static function Relationship(Pattern $a, Pattern $b, array $direction): Relationship {
        return new Relationship($a, $b, $direction);
    }

    /**
     * Creates the MATCH clause
     * @param object $pattern instance of Pattern or Pattern[]
     * @return static
     */
    public static function Match(object $pattern): self {
        $match = new MatchClause();
        if ( $pattern instanceof Pattern ) {
            $match->addPattern($pattern);
        } else if ( $pattern instanceof Array_ ) {
            foreach ($pattern as $p) {
                $match->addPattern($p);
            }
        }

        self::$clauses[] = $match;
        return new static();
    }

    /**
     * Creates the RETURN clause
     * @param Pattern $pattern
     * @return static
     */
    public static function Returning(Pattern $pattern): self {
        $return = new ReturnClause();
        $return->addColumn($pattern);
        self::$clauses[] = $return;
        return new static();
    }

    /**
     * Creates the CREATE clause
     * @param object $patterns instance of Pattern or Pattern[]
     * @return static
     */
    public static function Create(object $patterns): self {
        $create = new CreateClause();
        if ( $patterns instanceof Pattern ) {
            $create->addPattern($patterns);
        } else if ( $patterns instanceof Array_ ) {
            foreach ($patterns as $pattern) {
                $create->addPattern($pattern);
            }
        }

        self::$clauses[] = $create;
        return new static();
    }

    /**
     * Creates the DELETE clause
     * @param Pattern $pattern
     * @return static
     */
    public static function Delete(Pattern $pattern): self {
        $delete = new DeleteClause();
        $delete->setNode($pattern);
        self::$clauses[] = $delete;
        return new static();
    }

    /**
     * Creates the DETACH DELETE clause
     * @param Pattern $pattern
     * @return static
     */
    public static function DetachDelete(Pattern $pattern): self {
        $delete = new DeleteClause();
        $delete->setDetach(true);
        $delete->setNode($pattern);
        self::$clauses[] = $delete;
        return new static();
    }

    /**
     * Creates the LIMIT clause
     * @param Expression $expression
     * @return static
     */
    public static function Limit(Expression $expression): self {
        $limit = new LimitClause();
        $limit->setExpression($expression);
        self::$clauses[] = $limit;
        return new static();
    }

    /**
     * Creates the MERGE clause
     * @param Pattern $pattern
     * @return static
     */
    public static function Merge(Pattern $pattern): self {
        $merge = new MergeClause();
        $merge->setPattern($pattern);
        self::$clauses[] = $merge;
        return new static();
    }

    /**
     * Creates the OPTIONAL MATCH clause
     * @param object $patterns Pattern or Pattern[]
     * @return static
     */
    public static function OptionalMatch(object $patterns): self {
        $match = new OptionalMatchClause();
        if ( $patterns instanceof Pattern) {
            $match->addPattern($patterns);
        } else if ( $patterns instanceof Array_ ) {
            foreach ($patterns as  $pattern) {
                $match->addPattern($pattern);
            }
        }

        self::$clauses[] = $match;
        return new static();
    }


    public static function OrderBy(object $properties, bool $descending): self {
        $orderBy = new OrderByClause();

        self::$clauses[] = $orderBy;
        return new static();
    }

    /**
     * Creates the REMOVE clause
     * @param Expression $expression
     * @return static
     */
    public static function Remove(Expression $expression): self {
        $remove = new RemoveClause();
        $remove->setExpression($expression);

        self::$clauses[] = $remove;
        return new static();
    }

    /**
     * Create the SET clause
     * @param object $expression
     * @return static
     */
    public static function Set(object $expression): self {
        $set = new SetClause();
        if ( $expression instanceof Expression ) {
            $set->addExpression($expression);
        } else if ( $expression instanceof Array_ ) {
            foreach ($expression as $e) {
                $set->addExpression($e);
            }
        }

        self::$clauses[] = $set;
        return new static();
    }

    /**
     * Creates the WHERE clause
     * @param Pattern $pattern
     * @return static
     */
    public static function Where(Pattern $pattern): self {
        $where = new WhereClause();
        $where->setPattern($pattern);

        self::$clauses[] = $where;
        return new static();
    }

    public static function With(Expression $expression): self {
        $with = new WithClause();
        $with->addEntry($expression);
        self::$clauses[] = $with;
        return new static();
    }

    public static function Build(): string {
        $res = "";
        foreach( self::$clauses as $clause ) {
            $res.= $clause->toQuery() . " ";
        }
        self::$clauses = [];
        return $res;
    }
}