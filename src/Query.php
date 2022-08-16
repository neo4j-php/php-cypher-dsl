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
use WikibaseSolutions\CypherDSL\Expressions\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

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
    public const function = Procedure::class;

    /**
     * @var Clause[] $clauses Ordered list of clauses for this query
     */
    private array $clauses = [];

    /**
     * @see Query::new()
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct()
    {
        // This constructor currently does nothing, but we still define it, so we can mark it as internal
    }

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
     * You can also directly use the constructors of the most basic types, namely:
     *
     *  Query::boolean() - For a boolean
     *  Query::string() - For a string
     *  Query::integer() - For an integer
     *  Query::float() - For a float
     *  Query::list() - For a list
     *  Query::map() - For a map
     *
     * @param string|int|float|bool|array|null $literal The literal to construct
     * @return String_|Boolean|Float_|Integer|Map|List_|Literal|string The string literal that was created (or a
     *  reference to the Literal class)
     */
    public static function literal($literal = null)
    {
        if ($literal === null) {
            return self::literal;
        }

        return Literal::literal($literal);
    }

    /**
     * Creates a new boolean.
     *
     * @param bool $value
     * @return Boolean
     */
    public static function boolean(bool $value): Boolean
    {
        return self::literal()::boolean($value);
    }

    /**
     * Creates a new string.
     *
     * @param string $value
     * @return String_
     */
    public static function string(string $value): String_
    {
        return self::literal()::string($value);
    }

    /**
     * Creates a new integer.
     *
     * @param int $value
     * @return Integer
     */
    public static function integer(int $value): Integer
    {
        return self::literal()::integer($value);
    }

    /**
     * Creates a new float.
     *
     * @param float $value
     * @return Float_
     */
    public static function float(float $value): Float_
    {
        return self::literal()::float($value);
    }

    /**
     * Creates a new list literal.
     *
     * @param array $value
     * @return List_
     */
    public static function list(array $value): List_
    {
        return self::literal()::list($value);
    }

    /**
     * Creates a new map literal.
     *
     * @param array $value
     * @return Map
     */
    public static function map(array $value): Map
    {
        return self::literal()::map($value);
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
     * Returns the name of the Func class. This can be used to more easily create new functions calls, like so:
     *
     * Query::function()::raw(...)
     *
     * @return string|Procedure
     */
    public static function function(): string
    {
        return self::function;
    }

    /**
     * Creates a raw expression.
     *
     * @param string $expression The raw expression
     * @return RawExpression
     */
    public static function rawExpression(string $expression): RawExpression
    {
        return new RawExpression($expression);
    }

    /**
     * Creates an EXISTS expression.
     *
     * @param CompletePattern|CompletePattern[]|MatchClause $match
     * @param BooleanType|WhereClause|null $where
     * @param bool $insertParentheses
     * @return Exists
     */
    public static function exists($match, $where = null, bool $insertParentheses = false): Exists
    {
        if (!$match instanceof MatchClause) {
            $match = is_array($match) ? $match : [$match];
            $match = (new MatchClause())->addPattern(...$match);
        }

        if (!$where instanceof WhereClause && $where !== null) {
            $where = (new WhereClause())->addExpression($where);
        }

        return new Exists($match, $where, $insertParentheses);
    }

    /**
     * Creates a CALL sub query clause and adds it to the query.
     *
     * @param Query|callable(Query):void $query A callable decorating a query, or the actual CALL subquery
     * @param Variable|Pattern|string|Variable[]|Pattern[]|string[] $variables The variables to include in the WITH clause for correlation
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

        $callClause = new CallClause();
        $callClause->withSubQuery($subQuery);
        $callClause->addWithVariable(...$variables);

        $this->clauses[] = $callClause;

        return $this;
    }

    /**
     * Creates the CALL procedure clause.
     *
     * @param Procedure $procedure The procedure to call
     * @param string|Variable|string[]|Variable[] $yields The result fields that should be returned
     * @return Query
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 122)
     */
    public function callProcedure(Procedure $procedure, $yields = []): self
    {
        if (!is_array($yields)) {
            $yields = [$yields];
        }

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure($procedure);
        $callProcedureClause->addYield(...$yields);

        $this->clauses[] = $callProcedureClause;

        return $this;
    }

    /**
     * Creates the MATCH clause.
     *
     * @param CompletePattern|CompletePattern[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 57)
     */
    public function match($patterns): self
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        $matchClause = new MatchClause();
        $matchClause->addPattern(...$patterns);

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
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $returnClause = new ReturnClause();
        $returnClause->addColumn(...$expressions);
        $returnClause->setDistinct($distinct);

        $this->clauses[] = $returnClause;

        return $this;
    }

    /**
     * Creates the CREATE clause.
     *
     * @param CompletePattern|CompletePattern[] $patterns A single pattern or a list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/
     */
    public function create($patterns): self
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        $createClause = new CreateClause();
        $createClause->addPattern(...$patterns);
        
        $this->clauses[] = $createClause;

        return $this;
    }

    /**
     * Creates the DELETE clause.
     *
     * @param StructuralType|Pattern|StructuralType[]|Pattern[] $structures The structures to delete
     * @param bool $detach Whether to DETACH DELETE
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 105)
     */
    public function delete($structures, bool $detach = false): self
    {
        if (!is_array($structures)) {
            $structures = [$structures];
        }

        $deleteClause = new DeleteClause();
        $deleteClause->setDetach($detach);
        $deleteClause->addStructure(...$structures);
        
        $this->clauses[] = $deleteClause;

        return $this;
    }

    /**
     * Creates the DETACH DELETE clause.
     *
     * @param StructuralType|Pattern|StructuralType[]|Pattern[] $variables The variables to delete, including relationships
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
     * Adds a LIMIT clause.
     *
     * @param NumeralType|int $limit The amount to use as the limit
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 98)
     */
    public function limit($limit): self
    {
        $limitClause = new LimitClause();
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
        $skipClause->setSkip($amount);

        $this->clauses[] = $skipClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
     *
     * @param CompletePattern $pattern The pattern to merge
     * @param SetClause|null $createClause The clause to execute when the pattern is created
     * @param SetClause|null $matchClause The clause to execute when the pattern is matched
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 115)
     */
    public function merge(CompletePattern $pattern, SetClause $createClause = null, SetClause $matchClause = null): self
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
     * @param CompletePattern|CompletePattern[] $patterns A single pattern or a list of patterns
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
     * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 69)
     */
    public function optionalMatch($patterns): self
    {
        if (!is_array($patterns)) {
            $patterns = [$patterns];
        }

        $optionalMatchClause = new OptionalMatchClause();
        $optionalMatchClause->addPattern(...$patterns);

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
        if (!is_array($properties)) {
            $properties = [$properties];
        }

        $orderByClause = new OrderByClause();
        $orderByClause->setDescending($descending);
        $orderByClause->addProperty(...$properties);

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
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $removeClause = new RemoveClause();
        $removeClause->addExpression(...$expressions);

        $this->clauses[] = $removeClause;

        return $this;
    }

    /**
     * Create the SET clause.
     *
     * @param PropertyReplacement|Label|PropertyReplacement[]|Label[] $expressions A single expression or a list of expressions
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/
     *
     */
    public function set($expressions): self
    {
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $setClause = new SetClause();
        $setClause->add(...$expressions);

        $this->clauses[] = $setClause;

        return $this;
    }

    /**
     * Creates the WHERE clause.
     *
     * @param BooleanType|BooleanType[] $expressions The expression to match
     * @param string $operator The operator with which to unify the given expressions, should be either WhereClause::OR,
     *  WhereClause::AND or WhereClause::XOR
     *
     * @return $this
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/
     */
    public function where($expressions, string $operator = WhereClause::AND): self
    {
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $whereClause = new WhereClause();

        foreach ($expressions as $expression) {
            $whereClause->addExpression($expression, $operator);
        }

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
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $withClause = new WithClause();
        $withClause->addEntry(...$expressions);

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
        if (is_callable($queryOrCallable)) {
            $query = Query::new();
            $queryOrCallable($query);
        } else {
            $query = $queryOrCallable;
        }

        $unionClause = new UnionClause();
        $unionClause->setAll($all);

        $this->clauses[] = $unionClause;

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

        // Filter any empty clauses to prevent double spaces
        return implode(" ", array_filter($builtClauses, fn ($clause) => !empty($clause)));
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
}
