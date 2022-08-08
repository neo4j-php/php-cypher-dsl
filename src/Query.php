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

use Closure;
use WikibaseSolutions\CypherDSL\Clauses\CallClause;
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
use WikibaseSolutions\CypherDSL\Clauses\UnionClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Expressions\Functions\Func;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Decimal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\DateType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\LocalDateTimeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PointType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\TimeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * Builder class for building complex Cypher queries.
 */
final class Query implements QueryConvertible
{
    use EscapeTrait;
    use ErrorTrait;

    // A reference to the Literal class
    public const literal = Literal::class;

    // A reference to the FunctionCall class
    public const function = Func::class;

    /**
     * @var Clause[] $clauses Ordered list of clauses for this query
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
     * @param string|null $label The label to give to the node
     *
     * @return Node
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node
     */
    public static function node(string $label = null): Node
    {
        return new Node($label);
    }

    /**
     * Creates a relationship.
     *
     * @param array $direction The direction of the relationship, should be either:
     *  - Path::DIR_RIGHT (for a relation of (a)-->(b))
     *  - Path::DIR_LEFT (for a relation of (a)<--(b))
     *  - Path::DIR_UNI (for a relation of (a)--(b))
     *
     * @return Relationship
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship
     */
    public static function relationship(array $direction): Relationship
    {
        return new Relationship($direction);
    }

    /**
     * Creates a new variable with the given name, or generates a new variable with a random unique name.
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
     * This function cannot be used directly to construct Point or Date types. Instead, you can create a Point literal
     * by using any of the following functions:
     *
     *  Query::literal()::point2d(...) - For a 2D cartesian point
     *  Query::literal()::point3d(...) - For a 3D cartesian point
     *  Query::literal()::point2dWGS84(...) - For a 2D WGS 84 point
     *  Query::literal()::point3dWGS84(...) - For a 3D WGS 84 point
     *
     * And a Date literal by using one of the following functions:
     *
     *  Query::literal()::date(...) - For the current date
     *  Query::literal()::dateYMD(...) - For a date from the given year, month and day
     *  Query::literal()::dateYWD(...) - For a date from the given year, week and day
     *  Query::literal()::dateString(...) - For a date from the given date string
     *  Query::literal()::dateTime(...) - For the current datetime
     *  Query::literal()::dateTimeYMD(...) - For a datetime from the given parameters (see function definition)
     *  Query::literal()::dateTimeYWD(...) - For a datetime from the given parameters (see function definition)
     *  Query::literal()::dateTimeYQD(...) - For a datetime from the given parameters (see function definition)
     *  Query::literal()::dateTimeYD(...) - For a datetime from the given parameters (see function definition)
     *  Query::literal()::dateTimeString(...) - For a datetime from the given datetime string
     *  Query::literal()::localDateTime(...) - For the current local datetime
     *  Query::literal()::localDateTimeYMD(...) - For a local datetime from the given parameters (see function definition)
     *  Query::literal()::localDateTimeYWD(...) - For a local datetime from the given parameters (see function definition)
     *  Query::literal()::localDateTimeYQD(...) - For a local datetime from the given parameters (see function definition)
     *  Query::literal()::localDateTimeYD(...) - For a local datetime from the given parameters (see function definition)
     *  Query::literal()::localDateTimeString(...) - For a local datetime from the given datetime string
     *  Query::literal()::localTimeCurrent(...) - For the current LocalTime
     *  Query::literal()::localTime(...) - For a local time from the given parameters (see function definition)
     *  Query::literal()::localTimeString(...) - For a local time from the given time string
     *  Query::literal()::time(...) - For the curren time
     *  Query::literal()::timeHMS(...) - For a time from the given hour, minute and second
     *  Query::literal()::timeString(...) - For a time from the given time string
     *
     * When no arguments are given to this function (or NULL is passed as its only argument), the function will return
     * a reference to the Literal class.
     *
     * @param string|int|float|bool|array|null $literal The literal to construct
     * @return String_|Boolean|Decimal|Literal|Map|List_|string The string literal that was created (or a reference to
     *  the Literal class)
     */
    public static function literal($literal = null)
    {
        if ($literal === null) {
            return self::literal;
        }

        return Literal::literal($literal);
    }

    /**
     * Creates a list.
     *
     * @param array $values An array of values from which to construct the list
     * @return List_
     * @see Query::literal() for a function that automatically determines the class to construct
     */
    public static function list(array $values): List_
    {
        return Literal::literal($values);
    }

    /**
     * Creates a map.
     *
     * @param array $values The map of properties as a number of key-expression pairs
     * @return Map
     * @see Query::literal() for a function that automatically determines the class to construct
     */
    public static function map(array $values): Map
    {
        return new Map($values);
    }

    /**
     * Creates a parameter.
     *
     * @param string|null $parameter The name of the parameter; may only consist of alphanumeric characters and underscores
     * @return Parameter
     */
    public static function parameter(?string $parameter = null): Parameter
    {
        return new Parameter($parameter);
    }

    /**
     * Returns the name of the FunctionCall class. This can be used to more easily create new functions calls, like so:
     *
     * Query::function()::raw(...)
     *
     * @return Func|string
     */
    public static function function(): string
    {
        return self::function;
    }

    /**
     * Creates a raw expression.
     *
     * @param string $expression The raw expression
     * @return ListType|MapType|BooleanType|DateTimeType|DateType|LocalDateTimeType|PointType|NumeralType|StringType|TimeType|NodeType|PathType|RelationshipType
     */
    public static function rawExpression(string $expression): AnyType
    {
        return new RawExpression($expression);
    }

    /**
     * Creates a CALL sub query clause and adds it to the query.
     *
     * @param Query|callable(Query):void $query A callable decorating a query, or the actual CALL subquery
     * @param Variable|Variable[]|string $variables The variables to include in the WITH clause for correlation
     *
     * @return Query
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/
     * @see CallClause
     */
    public function call($query = null, $variables = []): self
    {
        $this->assertClass('query', [Query::class, Closure::class, 'callable'], $query);

        if (is_callable($query)) {
            $subQuery = self::new();
            $query($subQuery);
        } else {
            $subQuery = $query;
        }

        if (!is_array($variables)) {
            $variables = [$variables];
        }

        $variables = array_map(function ($variable): Variable {
            self::assertClass();

            if (is_string($variable)) {
                return self::variable($variable);
            }

            return $variable->getVariable();
        }, $variables);

        $callClause = new CallClause();
        $callClause->withSubQuery($subQuery);
        $callClause->withVariables($variables);

        $this->clauses[] = $callClause;

        return $this;
    }

    /**
     * Creates the CALL procedure clause.
     *
     * @param string $procedure The procedure to call
     * @param AnyType[]|AnyType|string[]|string|bool[]|bool|float[]|float|int[]|int $arguments The arguments to pass to the procedure (ignored if $procedure is of type FunctionCall)
     * @param Variable[]|Variable|HasVariable[]|HasVariable|string[]|string $yields The result field that will be returned
     *
     * @return Query
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
     */
    public function callProcedure(string $procedure, $arguments = [], $yields = []): self
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure($procedure);

        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }

        $arguments = array_map(function ($argument): AnyType {
            return $argument instanceof AnyType ? $argument : self::literal($argument);
        }, $arguments);

        if (!is_array($yields)) {
            $yields = [$yields];
        }

        $yields = array_map(function ($yield): Variable {
            $this->assertClass('yields', [Variable::class, HasVariable::class, 'string'], $yield);

            if (is_string($yield)) {
                return self::variable($yield);
            }

            if ($yield instanceof HasVariable) {
                return $yield->getVariable();
            }

            return $yield;
        }, $yields);

        $callProcedureClause->setArguments($arguments);
        $callProcedureClause->setYields($yields);

        $this->clauses[] = $callProcedureClause;

        return $this;
    }

    /**
     * Creates the MATCH clause.
     *
     * @param PathType|NodeType|PathType[]|NodeType[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
     */
    public function match($patterns): self
    {
        $matchClause = new MatchClause();

        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        $matchClause->setPatterns($patterns);

        $this->clauses[] = $matchClause;

        return $this;
    }

    /**
     * Creates the RETURN clause.
     *
     * @param AnyType|AnyType[] $expressions The expressions to return; if the array-key is
     *                                             non-numerical, it is used as the alias
     * @param bool $distinct
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/
     */
    public function returning($expressions, bool $distinct = false): self
    {
        $returnClause = new ReturnClause();

        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $expressions = array_map(function ($expression): AnyType {
            // If it has a variable, we want to put the variable in the RETURN clause instead of the
            // object itself. Theoretically, a node could be returned directly, but this is extremely
            // rare. If a user wants to do this, they can use the ReturnClause class directly.
            return $expression instanceof HasVariable ? $expression->getVariable() : $expression;
        }, $expressions);

        $returnClause->setColumns($expressions);
        $returnClause->setDistinct($distinct);

        $this->clauses[] = $returnClause;

        return $this;
    }

    /**
     * Creates the CREATE clause.
     *
     * @param PathType|NodeType|PathType[]|NodeType[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/
     */
    public function create($patterns): self
    {
        $createClause = new CreateClause();

        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            $this->assertClass('pattern', [PathType::class, NodeType::class], $pattern);

            $createClause->addPattern($pattern);
        }

        $this->clauses[] = $createClause;

        return $this;
    }

    /**
     * Creates the DELETE clause.
     *
     * @param string|Variable|HasVariable|(string|Variable|HasVariable)[] $variables The nodes to delete
     * @param bool $detach Whether to DETACH DELETE
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
     *
     */
    public function delete($variables, bool $detach = false): self
    {
        $deleteClause = new DeleteClause();
        $deleteClause->setDetach($detach);

        if (!is_array($variables)) {
            $variables = [$variables];
        }

        foreach ($variables as $variable) {
            $this->assertClass('variable', ['string', Variable::class, HasVariable::class], $variable);

            if (is_string($variable)) {
                $variable = Query::variable($variable);
            } elseif ($variable instanceof HasVariable) {
                $variable = $variable->getVariable();
            }

            $deleteClause->addVariable($variable);
        }

        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the DETACH DELETE clause.
     *
     * @param string|Variable|HasVariable|(string|Variable|HasVariable)[] $variables The variables to delete, including relationships
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
     * @deprecated Use Query::delete(..., true) instead
     */
    public function detachDelete($variables): self
    {
        return $this->delete($variables, true);
    }

    /**
     * Creates the LIMIT clause.
     *
     * @param NumeralType|int|float $limit The amount to use as the limit
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
     *
     */
    public function limit($limit): self
    {
        $limitClause = new LimitClause();

        if (is_float($limit) || is_int($limit)) {
            $limit = Literal::decimal($limit);
        }

        $limitClause->setLimit($limit);

        $this->clauses[] = $limitClause;

        return $this;
    }

    /**
     * Creates the SKIP clause.
     *
     * @param NumeralType|int|float $amount The amount to skip
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/skip/
     */
    public function skip($amount): self
    {
        $skipClause = new SkipClause();

        if (is_float($amount) || is_int($amount)) {
            $amount = Literal::decimal($amount);
        }

        $skipClause->setSkip($amount);

        $this->clauses[] = $skipClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
     *
     * @param PathType|NodeType $pattern The pattern to merge
     * @param Clause|null $createClause The clause to execute when the pattern is created
     * @param Clause|null $matchClause The clause to execute when the pattern is matched
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
     *
     */
    public function merge($pattern, Clause $createClause = null, Clause $matchClause = null): self
    {
        $this->assertClass('pattern', [PathType::class, NodeType::class], $pattern);
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
     * @param PathType|NodeType|(PathType|NodeType)[] $patterns A single pattern or a list of patterns
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
            $this->assertClass('pattern', [PathType::class, NodeType::class], $pattern);

            $optionalMatchClause->addPattern($pattern);
        }

        $this->clauses[] = $optionalMatchClause;

        return $this;
    }

    /**
     * Creates the ORDER BY clause.
     *
     * @param Property|Property[] $properties A single property or a list of properties
     * @param bool $descending Whether to order in descending order
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
     * @param PropertyReplacement|Label|(Assignment|Label)[] $expressions A single expression or a list of expressions
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
            $this->assertClass('expression', [PropertyReplacement::class, Label::class], $expression);

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
     * @return Query
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/
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
                $expression = $expression->getVariable();
            }

            $alias = is_int($maybeAlias) ? "" : $maybeAlias;
            $withClause->addEntry($expression, $alias);
        }

        $this->clauses[] = $withClause;

        return $this;
    }

    /**
     * Creates a raw clause. This should only be used for features that are not implemented by the DSL.
     *
     * @param string $clause The name of the clause; for instance "MATCH"
     * @param string $subject The subject/body of the clause
     * @return Query
     */
    public function raw(string $clause, string $subject): self
    {
        $this->clauses[] = new RawClause($clause, $subject);

        return $this;
    }

    /**
     * Combines the result of this query with another one via a UNION clause.
     *
     * @param Query|callable(Query):void $queryOrCallable The callable decorating a fresh query instance or the query instance to be attached after the union clause.
     * @param bool $all Whether the union should include all results or remove the duplicates instead.
     *
     * @return Query
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/union/
     */
    public function union($queryOrCallable, bool $all = false): self
    {
        $unionClause = new UnionClause();
        $unionClause->setAll($all);

        $this->clauses[] = $unionClause;

        if (is_callable($queryOrCallable)) {
            $query = Query::new();
            $queryOrCallable($query);
        } else {
            $query = $queryOrCallable;
        }

        foreach ($query->getClauses() as $clause) {
            $this->clauses[] = $clause;
        }

        return $this;
    }

    /**
     * Add a clause to the query.
     *
     * @param Clause $clause The clause to add to the query
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
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return $this->build();
    }

    /**
     * Automatically build the query if this object is used as a string somewhere.
     *
     * @return string
     */
    public function __toString(): string
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
