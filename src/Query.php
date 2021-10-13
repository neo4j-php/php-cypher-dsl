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
use WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause;
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
use WikibaseSolutions\CypherDSL\Expressions\ExpressionList;
use WikibaseSolutions\CypherDSL\Expressions\Functions\FunctionCall;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Node;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Expressions\Variable;

class Query
{
    /**
     * @var Clause[] $clauses
     */
    public array $clauses = [];

    /**
     * Construct a new Query instance.
     *
     * @return Query
     */
    public static function new(): self
    {
        return new Query();
    }

    /**
     * Creates a node.
     *
     * @param string|null $label
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
     *
     * @return Node
     */
    public static function node(string $label = null): Node
    {
        return new Node($label);
    }

    /**
     * Creates a relationship.
     *
     * @param Pattern $a         The node left of the relationship
     * @param Pattern $b         The node right of the relationship
     * @param array   $direction The direction of the relationship, should be either:
     *                           - Relationship::DIR_RIGHT (for a relation of (a)-->(b))
     *                           - Relationship::DIR_LEFT (for a relation of (a)<--(b))
     *                           - Relationship::DIR_UNI (for a relation of (a)--(b))
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
     *
     * @return Relationship
     */
    public static function relationship(Pattern $a, Pattern $b, array $direction): Relationship
    {
        return new Relationship($a, $b, $direction);
    }

    /**
     * Creates a variable.
     *
     * @param  string $variable
     * @return Variable
     */
    public static function variable(string $variable): Variable
    {
        return new Variable($variable);
    }

    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * @param  integer|float|double|bool|string $literal The literal to construct
     * @return Literal
     */
    public static function literal($literal): Literal
    {
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
     * Creates a list of expressions.
     *
     * @param  Expression[] $expressions
     * @return ExpressionList
     */
    public static function list(array $expressions): ExpressionList
    {
        return new ExpressionList($expressions);
    }

    /**
     * Creates a property map.
     *
     * @param  Expression[] $properties The map of properties as a number of key-expression pairs
     * @return PropertyMap
     */
    public static function map(array $properties): PropertyMap
    {
        return new PropertyMap($properties);
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
    public function match($patterns): self
    {
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
     * @param Expression[]|Expression $expressions The expressions to return; if the array-key is
     *                                             non-numerical, it is used as the alias
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
     *
     * @return $this
     */
    public function returning($expressions, bool $distinct = false): self
    {
        $returnClause = new ReturnClause();

        if ($expressions instanceof Expression) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $maybeAlias => $expression) {
            $alias = is_integer($maybeAlias) ? "" : $maybeAlias;
            $returnClause->addColumn($expression, $alias);
        }

        $returnClause->setDistinct($distinct);

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
    public function create($patterns): self
    {
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
    public function delete($expressions): self
    {
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
    public function detachDelete($expressions): self
    {
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
    public function limit(Expression $expression): self
    {
        $limitClause = new LimitClause();
        $limitClause->setExpression($expression);

        $this->clauses[] = $limitClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
     *
     * @param Pattern     $pattern      The pattern to merge
     * @param Clause|null $createClause The clause to execute when the pattern is created
     * @param Clause|null $matchClause  The clause to execute when the pattern is matched
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
     *
     * @return $this
     */
    public function merge(Pattern $pattern, Clause $createClause = null, Clause $matchClause = null): self
    {
        $mergeClause = new MergeClause();
        $mergeClause->setPattern($pattern);

        if (isset($createClause)) {
            $mergeClause->setOnCreate($createClause);
        }

        if (isset($matchClause)) {
            $mergeClause->setOnMatch($matchClause);
        }

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
    public function optionalMatch($patterns): self
    {
        $optionalMatchClause = new OptionalMatchClause();

        if ($patterns instanceof Pattern) {
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
     * @param bool                $descending Whether or not to order in a descending order
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
     *
     * @return $this
     */
    public function orderBy($properties, bool $descending = false): self
    {
        $orderByClause = new OrderByClause();
        $orderByClause->setDescending($descending);

        if ($properties instanceof Property ) {
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
     * @param Expression[]|Expression $expressions The expressions to remove (should either be a Node or a Property)
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/
     *
     * @return $this
     */
    public function remove($expressions): self
    {
        $removeClause = new RemoveClause();

        if ($expressions instanceof Expression) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $expression) {
            $removeClause->addExpression($expression);
        }

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
    public function set($expressions): self
    {
        $setClause = new SetClause();

        if ($expressions instanceof Expression ) {
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
    public function where(Expression $expression): self
    {
        $whereClause = new WhereClause();
        $whereClause->setExpression($expression);

        $this->clauses[] = $whereClause;

        return $this;
    }

    /**
     * Creates the WITH clause.
     *
     * @param Expression[]|Expression $expressions The entries to add; if the array-key is
     *                                             non-numerical, it is used as the alias
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
     *
     * @return Query
     */
    public function with($expressions): self
    {
        $withClause = new WithClause();

        if ($expressions instanceof Expression) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $maybeAlias => $expression) {
            $alias = is_integer($maybeAlias) ? "" : $maybeAlias;
            $withClause->addEntry($expression, $alias);
        }

        $this->clauses[] = $withClause;

        return $this;
    }

    /**
     * Creates the CALL procedure clause.
     *
     * @param string $procedure The procedure to call
     * @param array  $arguments The arguments passed to the procedure
     * @param array  $yields    The results field that will be returned
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
     *
     * @return Query
     */
    public function callProcedure(string $procedure, array $arguments = [], array $yields = []): self
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure($procedure);
        $callProcedureClause->withArguments($arguments);
        $callProcedureClause->yields($yields);

        $this->clauses[] = $callProcedureClause;

        return $this;
    }

    /**
     * Add a clause to the query.
     *
     * @param  Clause $clause
     * @return Query
     */
    public function addClause(Clause $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    /**
     * Builds the query.
     *
     * @return string The fully constructed query
     */
    public function build(): string
    {
        return implode(
            " ", array_map(
                fn(Clause $clause): string => $clause->toQuery(),
                $this->clauses
            )
        );
    }
}