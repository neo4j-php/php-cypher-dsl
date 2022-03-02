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

use WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause;
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Clauses\DeleteClause;
use WikibaseSolutions\CypherDSL\Clauses\LimitClause;
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Clauses\OptionalMatchClause;
use WikibaseSolutions\CypherDSL\Clauses\OrderByClause;
use WikibaseSolutions\CypherDSL\Clauses\RawClause;
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Clauses\ReturnClause;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Clauses\SkipClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Functions\FunctionCall;
use WikibaseSolutions\CypherDSL\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Literals\StringLiteral;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * Builder class for building complex Cypher queries.
 */
class Query implements QueryConvertable
{
    use EscapeTrait;
    use ErrorTrait;

    // A reference to the Literal class
    public const literal = Literal::class;

    // A reference to the FunctionCall class
    public const function = FunctionCall::class;

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
     * @return Node
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
     *
     */
    public static function node(string $label = null): Node
    {
        return new Node($label);
    }

    /**
     * Creates a relationship.
     *
     * @param StructuralType $a The node left of the relationship
     * @param StructuralType $b The node right of the relationship
     * @param array $direction The direction of the relationship, should be either:
     *                           - Path::DIR_RIGHT (for a relation of (a)-->(b))
     *                           - Path::DIR_LEFT (for a relation of (a)<--(b))
     *                           - Path::DIR_UNI (for a relation of (a)--(b))
     *
     * @return Path
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
     *
     */
    public static function relationship(StructuralType $a, StructuralType $b, array $direction): Path
    {
        return new Path($a, $b, $direction);
    }

    /**
     * Creates a variable.
     *
     * @param string|null $variable The name of the variable; leave empty to automatically generate a variable name.
     * @return Variable
     */
    public static function variable(?string $variable = null): Variable
    {
        return new Variable($variable);
    }

    /**
     * Creates a new literal from the given value. This function automatically constructs the appropriate
     * class based on the type of the value given.
     *
     * This function cannot be used directly to construct Point or Date types.
     *
     * You can create a Point literal by using any of the following functions:
     *
     *  Query::literal()::point2d(...) - For a 2D cartesian point
     *  Query::literal()::point3d(...) - For a 3D cartesian point
     *  Query::literal()::point2dWGS84(...) - For a 2D WGS 84 point
     *  Query::literal()::point3dWGS84(...) - For a 3D WGS 84 point
     *
     * @param mixed $literal The literal to construct
     * @return StringLiteral|Boolean|Decimal|Literal|string
     */
    public static function literal($literal = null)
    {
        if ($literal === null) {
            return self::literal;
        }

        return Literal::literal($literal);
    }

    /**
     * Creates a list of expressions.
     *
     * @param iterable $values
     * @return ExpressionList
     */
    public static function list(iterable $values): ExpressionList
    {
        $expressions = [];
        foreach ($values as $value) {
            $expressions[] = $value instanceof AnyType ?
                $value : self::literal($value);
        }

        return new ExpressionList($expressions);
    }

    /**
     * Creates a property map.
     *
     * @param AnyType[] $properties The map of properties as a number of key-expression pairs
     * @return PropertyMap
     */
    public static function map(array $properties): PropertyMap
    {
        return new PropertyMap($properties);
    }

    /**
     * Creates a parameter.
     *
     * @param string $parameter The name of the parameter; may only consist of alphanumeric characters and
     *                           underscores
     * @return Parameter
     */
    public static function parameter(string $parameter): Parameter
    {
        return new Parameter($parameter);
    }

    /**
     * Returns the name of the FunctionCall class. This can be used to more easily create new functions calls, like so:
     *
     * Query::function()::raw(...)
     *
     * @return FunctionCall
     */
    public static function function(): string
    {
        return self::function;
    }

    /**
     * Creates a raw expression.
     *
     * @param string $expression The raw expression
     * @return ListType|MapType|BooleanType|NumeralType|StringType|NodeType|PathType
     */
    public static function rawExpression(string $expression): AnyType
    {
        return new RawExpression($expression);
    }

    /**
     * Creates the MATCH clause.
     *
     * @param StructuralType|StructuralType[]|Assignment|Assignment[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
     *
     */
    public function match($patterns): self
    {
        $matchClause = new MatchClause();

        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            $this->assertClass('pattern', [StructuralType::class, Assignment::class], $pattern);

            $matchClause->addPattern($pattern);
        }

        $this->clauses[] = $matchClause;

        return $this;
    }

    /**
     * Creates the RETURN clause.
     *
     * @param AnyType[]|AnyType $expressions The expressions to return; if the array-key is
     *                                             non-numerical, it is used as the alias
     * @param bool $distinct
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
     */
    public function returning($expressions, bool $distinct = false): self
    {
        $returnClause = new ReturnClause();

        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $maybeAlias => $expression) {
            $this->assertClass('expression', AnyType::class, $expression);

            if ($expression instanceof Node) {
                $expression = $expression->getName();
            }

            $alias = is_int($maybeAlias) ? "" : $maybeAlias;
            $returnClause->addColumn($expression, $alias);
        }

        $returnClause->setDistinct($distinct);

        $this->clauses[] = $returnClause;

        return $this;
    }

    /**
     * Creates the CREATE clause.
     *
     * @param StructuralType|StructuralType[]|Assignment|Assignment[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/
     *
     */
    public function create($patterns): self
    {
        $createClause = new CreateClause();

        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            $this->assertClass('pattern', [StructuralType::class, Assignment::class], $pattern);

            $createClause->addPattern($pattern);
        }

        $this->clauses[] = $createClause;

        return $this;
    }

    /**
     * Creates the DELETE clause.
     *
     * @param NodeType|NodeType[] $nodes The nodes to delete
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
     *
     */
    public function delete($nodes): self
    {
        $deleteClause = new DeleteClause();

        if (!is_array($nodes)) {
            $nodes = [$nodes];
        }

        foreach ($nodes as $node) {
            $this->assertClass('node', NodeType::class, $node);

            $deleteClause->addNode($node);
        }

        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the DETACH DELETE clause.
     *
     * @param NodeType|NodeType[] $nodes The nodes to delete
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
     *
     */
    public function detachDelete($nodes): self
    {
        $deleteClause = new DeleteClause();
        $deleteClause->setDetach(true);

        if (!is_array($nodes)) {
            $nodes = [$nodes];
        }

        foreach ($nodes as $node) {
            $this->assertClass('node', NodeType::class, $node);

            $deleteClause->addNode($node);
        }

        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the LIMIT clause.
     *
     * @param NumeralType $limit The amount to use as the limit
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
     *
     */
    public function limit(NumeralType $limit): self
    {
        $limitClause = new LimitClause();
        $limitClause->setLimit($limit);

        $this->clauses[] = $limitClause;

        return $this;
    }

    /**
     * Creates the SKIP clause.
     *
     * @param NumeralType $limit The amount to use as the limit
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/skip/
     */
    public function skip(NumeralType $limit): self
    {
        $skipClause = new SkipClause();
        $skipClause->setSkip($limit);

        $this->clauses[] = $skipClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
     *
     * @param StructuralType|Assignment $pattern The pattern to merge
     * @param Clause|null $createClause The clause to execute when the pattern is created
     * @param Clause|null $matchClause The clause to execute when the pattern is matched
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
     *
     */
    public function merge($pattern, Clause $createClause = null, Clause $matchClause = null): self
    {
        $this->assertClass('pattern', [StructuralType::class, Assignment::class], $pattern);

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
     * @param StructuralType|StructuralType[]|Assignment|Assignment[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
     *
     */
    public function optionalMatch($patterns): self
    {
        $optionalMatchClause = new OptionalMatchClause();

        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            $this->assertClass('pattern', [StructuralType::class, Assignment::class], $pattern);

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
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/
     *
     */
    public function orderBy($properties, bool $descending = false): self
    {
        $orderByClause = new OrderByClause();
        $orderByClause->setDescending($descending);

        if (!is_array($properties)) {
            $properties = [$properties];
        }

        foreach ($properties as $property) {
            $this->assertClass('property', Property::class, $property);

            $orderByClause->addProperty($property);
        }

        $this->clauses[] = $orderByClause;

        return $this;
    }

    /**
     * Creates the REMOVE clause.
     *
     * @param Property|Label|Property[]|Label[] $expressions The expressions to remove (should either be a Node or a Property)
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/
     *
     */
    public function remove($expressions): self
    {
        $removeClause = new RemoveClause();

        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $expression) {
            $this->assertClass('expression', [Property::class, Label::class], $expression);

            $removeClause->addExpression($expression);
        }

        $this->clauses[] = $removeClause;

        return $this;
    }

    /**
     * Create the SET clause.
     *
     * @param Assignment|Label|(Assignment|Label)[] $expressions A single expression or a list of expressions
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
     *
     */
    public function set($expressions): self
    {
        $setClause = new SetClause();

        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $expression) {
            $this->assertClass('expression', [Assignment::class, Label::class], $expression);

            $setClause->addAssignment($expression);
        }

        $this->clauses[] = $setClause;

        return $this;
    }

    /**
     * Creates the WHERE clause.
     *
     * @param AnyType $expression The expression to match
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/
     *
     */
    public function where(AnyType $expression): self
    {
        $whereClause = new WhereClause();
        $whereClause->setExpression($expression);

        $this->clauses[] = $whereClause;

        return $this;
    }

    /**
     * Creates the WITH clause.
     *
     * @param AnyType[]|AnyType $expressions The entries to add; if the array-key is non-numerical, it is used as the alias
     *
     *
     * @return Query
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
     *
     */
    public function with($expressions): self
    {
        $withClause = new WithClause();

        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        foreach ($expressions as $maybeAlias => $expression) {
            $this->assertClass('expression', AnyType::class, $expression);

            if ($expression instanceof Node) {
                $expression = $expression->getName();
            }

            $alias = is_int($maybeAlias) ? "" : $maybeAlias;
            $withClause->addEntry($expression, $alias);
        }

        $this->clauses[] = $withClause;

        return $this;
    }

    /**
     * Creates a "RAW" query.
     *
     * @param string $clause The name of the clause; for instance "MATCH"
     * @param string $subject The subject/body of the clause
     * @return Query
     */
    public function raw(string $clause, string $subject)
    {
        $this->clauses[] = new RawClause($clause, $subject);

        return $this;
    }

    /**
     * Creates the CALL procedure clause.
     *
     * @param string $procedure The procedure to call
     * @param AnyType[] $arguments The arguments passed to the procedure
     * @param Variable[] $yields The results field that will be returned
     *
     * @return Query
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
     *
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
     * @param Clause $clause
     * @return Query
     */
    public function addClause(Clause $clause): self
    {
        $this->clauses[] = $clause;

        return $this;
    }

    /**
     * Returns the clauses in the query.
     *
     * @return Clause[]
     */
    public function getClauses(): array
    {
        return $this->clauses;
    }

    /**
     * Converts the object into a (partial) query.
     *
     * @return string
     */
    public function toQuery(): string
    {
        return $this->build();
    }

    /**
     * Builds the query.
     *
     * @return string The fully constructed query
     */
    public function build(): string
    {
        $builtClauses = array_map(
            fn (Clause $clause): string => $clause->toQuery(),
            $this->clauses
        );

        return implode(
            " ",
            array_filter($builtClauses, fn ($clause) => !empty($clause))
        );
    }
}
