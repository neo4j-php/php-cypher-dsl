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

use InvalidArgumentException;
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
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Expressions\Property;

class Query
{
    /**
     * @var Clause[] $clauses
     */
    public array $clauses = [];

    /**
     * Creates a node.
	 *
     * @param string|null $label
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
	 *
     * @return Node
     */
    public static function node(string $label = null): Node {
        return new Node($label);
    }

	/**
	 * Creates a relationship.
	 *
	 * @param Pattern $a The node left of the relationship
	 * @param Pattern $b The node right of the relationship
	 * @param array $direction The direction of the relationship, should be either:
	 *
	 * - Relationship::DIR_RIGHT (for a relation of (a)-->(b))
	 * - Relationship::DIR_LEFT (for a relation of (a)<--(b))
	 * - Relationship::DIR_UNI (for a relation of (a)--(b))
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
	 *
	 * @return Relationship
	 */
    public static function relationship(Pattern $a, Pattern $b, array $direction): Relationship {
        return new Relationship($a, $b, $direction);
    }

	/**
	 * Creates a new literal from the given value. This function automatically constructs the appropriate
	 * class based on the type of the value given.
	 *
	 * @param integer|float|double|bool|string $literal The literal to construct
	 * @return Literal
	 */
    public static function literal($literal): Literal {
		$literalType = gettype($literal);

		switch ($literalType) {
			case "string":
				return new StringLiteral($literal);
			case "boolean":
				return new Boolean($literal);
			case "double":
			case "float":
			case "integer":
				return new Decimal($literal);
			default:
				throw new InvalidArgumentException("The literal type " . $literalType . " is not supported by Cypher");
		}
	}

    /**
     * Creates the MATCH clause.
	 *
     * @param Pattern|Pattern[] $patterns A single pattern or a list of patterns
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
	 *
     * @return $this
     */
    public function match($patterns): self {
    	$matchClause = new MatchClause();

        if ($patterns instanceof Pattern) {
        	$patterns = [$patterns];
		}

        foreach ($patterns as $pattern) {
			$matchClause->addPattern($pattern);
		}

        $this->clauses[] = $matchClause;

        return $this;
    }

	/**
	 * Creates the RETURN clause.
	 *
	 * @param Expression $expression The expression to return
	 * @param string|null $alias The alias of this column
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
	 *
	 * @return $this
	 */
    public function returning(Expression $expression, string $alias = ""): self {
        $returnClause = new ReturnClause();
        $returnClause->addColumn($expression, $alias);

        $this->clauses[] = $returnClause;

        return $this;
    }

    /**
     * Creates the CREATE clause.
	 *
     * @param Pattern|Pattern[] $patterns A single pattern or a list of patterns
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/
	 *
     * @return $this
     */
    public function create($patterns): self {
        $createClause = new CreateClause();

        if ($patterns instanceof Pattern) {
            $patterns = [$patterns];
        }

		foreach ($patterns as $pattern) {
			$createClause->addPattern($pattern);
		}

        $this->clauses[] = $createClause;

        return $this;
    }

    /**
     * Creates the DELETE clause.
	 *
     * @param Expression|Expression[] $expressions The nodes to delete
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
	 *
     * @return $this
     */
    public function delete($expressions): self {
        $deleteClause = new DeleteClause();

        if ($expressions instanceof Expression) {
        	$expressions = [$expressions];
		}

		foreach ($expressions as $expression) {
			$deleteClause->addNode($expression);
		}

        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the DETACH DELETE clause.
	 *
     * @param Expression|Expression[] $expressions The nodes to delete
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
	 *
     * @return $this
     */
    public function detachDelete($expressions): self {
        $deleteClause = new DeleteClause();
        $deleteClause->setDetach(true);

		if ($expressions instanceof Expression) {
			$expressions = [$expressions];
		}

		foreach ($expressions as $expression) {
			$deleteClause->addNode($expression);
		}

        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the LIMIT clause.
	 *
     * @param Expression $expression An expression that returns an integer
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
	 *
     * @return $this
     */
    public function limit(Expression $expression): self {
        $limitClause = new LimitClause();
        $limitClause->setExpression($expression);

        $this->clauses[] = $limitClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
	 *
     * @param Pattern $pattern The pattern to merge
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
	 *
     * @return $this
     */
    public function merge(Pattern $pattern): self {
        $mergeClause = new MergeClause();
        $mergeClause->setPattern($pattern);

        $this->clauses[] = $mergeClause;

        return $this;
    }

    /**
     * Creates the OPTIONAL MATCH clause.
	 *
     * @param Pattern|Pattern[] $patterns A single pattern or a list of patterns
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
	 *
     * @return $this
     */
    public function optionalMatch($patterns): self {
        $optionalMatchClause = new OptionalMatchClause();

        if ( $patterns instanceof Pattern) {
            $patterns = [$patterns];
        }

		foreach ($patterns as  $pattern) {
			$optionalMatchClause->addPattern($pattern);
		}

        $this->clauses[] = $optionalMatchClause;

        return $this;
    }

	/**
	 * Creates the ORDER BY clause.
	 *
	 * @param Property|Property[] $properties A single property or a list of properties
	 * @param bool $descending Whether or not to order in a descending order
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
	 *
	 * @return $this
	 */
    public function orderBy($properties, bool $descending = false): self {
        $orderByClause = new OrderByClause();
        $orderByClause->setDescending($descending);

        if ( $properties instanceof Property ) {
        	$properties = [$properties];
		}

        foreach ( $properties as $property ) {
        	$orderByClause->addProperty($property);
		}

        $this->clauses[] = $orderByClause;

        return $this;
    }

    /**
     * Creates the REMOVE clause.
	 *
     * @param Expression $expression The expression to remove (should either be a Node or a Property)
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/
	 *
     * @return $this
     */
    public function remove(Expression $expression): self {
        $removeClause = new RemoveClause();
        $removeClause->addExpression($expression);

        $this->clauses[] = $removeClause;

        return $this;
    }

    /**
     * Create the SET clause.
	 *
     * @param Expression|Expression[] $expressions A single expression or a list of expressions
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
	 *
     * @return $this
     */
    public function set($expressions): self {
        $setClause = new SetClause();

        if ( $expressions instanceof Expression ) {
            $expressions = [$expressions];
        }

		foreach ($expressions as $expression) {
			$setClause->addExpression($expression);
		}

       	$this->clauses[] = $setClause;

        return $this;
    }

    /**
     * Creates the WHERE clause.
	 *
     * @param Expression $expression The expression to match
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/
	 *
     * @return $this
     */
    public function where(Expression $expression): self {
        $whereClause = new WhereClause();
        $whereClause->setExpression($expression);

       	$this->clauses[] = $whereClause;

        return $this;
    }

	/**
	 * Creates the WITH clause.
	 *
	 * @param Expression $expression The entry to add
	 * @param string $alias An optional entry alias
	 *
	 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
	 *
	 * @return Query
	 */
    public function with(Expression $expression, string $alias = ""): self {
		$withClause = new WithClause();
		$withClause->addEntry($expression, $alias);

       	$this->clauses[] = $withClause;

        return $this;
    }

	/**
	 * Builds the query.
	 *
	 * @return string The fully constructed query
	 */
    public function build(): string {
    	return implode(" ", array_map(
			fn(Clause $clause): string => $clause->toQuery(),
			$this->clauses
		));
    }
}