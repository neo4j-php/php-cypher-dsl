<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL;

use Stringable;
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
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Boolean;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Float_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Integer;
use WikibaseSolutions\CypherDSL\Expressions\Literals\List_;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Parameter;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\AllShortest;
use WikibaseSolutions\CypherDSL\Patterns\AllShortestPaths;
use WikibaseSolutions\CypherDSL\Patterns\AnyPath;
use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Patterns\Shortest;
use WikibaseSolutions\CypherDSL\Patterns\ShortestGroups;
use WikibaseSolutions\CypherDSL\Patterns\ShortestPath;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * Builder class for building complex Cypher queries.
 */
final class Query implements QueryConvertible
{
    // A reference to the Literal class
    public const literal = Literal::class;

    // A reference to the Procedure class
    public const procedure = Procedure::class;

    // A reference to the Procedure class
    // @deprecated Use self::procedure instead
    public const function = self::procedure;

    /**
     * @var Clause[] Ordered list of clauses for this query
     */
    private array $clauses = [];

    /**
     * Construct a new Query instance.
     */
    public static function new(): self
    {
        return new self();
    }

    /**
     * Creates a node.
     *
     * @param null|string $label The label to give to the node
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node Corresponding documentation on Neo4j.com
     */
    public static function node(?string $label = null): Node
    {
        return new Node($label);
    }

    /**
     * Creates a relationship.
     *
     * @param Direction $direction The direction of the relationship (optional, default: unidirectional)
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
     */
    public static function relationship(Direction $direction = Direction::UNI): Relationship
    {
        return new Relationship($direction);
    }

    /**
     * Creates a unidirectional relationship.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
     */
    public static function relationshipUni(): Relationship
    {
        return new Relationship(Direction::UNI);
    }

    /**
     * Creates a right relationship.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
     */
    public static function relationshipTo(): Relationship
    {
        return new Relationship(Direction::RIGHT);
    }

    /**
     * Creates a left relationship.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-relationship Corresponding documentation on Neo4j.com
     */
    public static function relationshipFrom(): Relationship
    {
        return new Relationship(Direction::LEFT);
    }



    /**
     * Creates a shortestPath pattern.
     *
     * @param CompletePattern $pattern The pattern to find the shortest path for
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/shortestpath/ Corresponding documentation on Neo4j.com
     */
    public static function shortestPath(CompletePattern $pattern): ShortestPath
    {
        return new ShortestPath($pattern);
    }

    /**
     * Creates an allShortestPaths pattern.
     *
     * @param CompletePattern $pattern The pattern to find all shortest paths for
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/allshortestpaths/ Corresponding documentation on Neo4j.com
     */
    public static function allShortestPaths(CompletePattern $pattern): AllShortestPaths
    {
        return new AllShortestPaths($pattern);
    }

    /**
     * Creates a SHORTEST k construct.
     *
     * @param CompletePattern $pattern The pattern to find the shortest path for
     * @param int|IntegerType $k       The number of paths to match
     *
     * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/ Corresponding documentation on Neo4j.com
     */
    public static function shortest(CompletePattern $pattern, int|IntegerType $k = 1): Shortest
    {
        return new Shortest($pattern, $k);
    }

    /**
     * Creates an ALL SHORTEST construct.
     *
     * @param CompletePattern $pattern The pattern to find all shortest paths for
     *
     * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/ Corresponding documentation on Neo4j.com
     */
    public static function allShortest(CompletePattern $pattern): AllShortest
    {
        return new AllShortest($pattern);
    }

    /**
     * Creates a SHORTEST k GROUPS construct.
     *
     * @param CompletePattern $pattern The pattern to find the shortest groups for
     * @param int|IntegerType $k       The number of groups to match
     *
     * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/ Corresponding documentation on Neo4j.com
     */
    public static function shortestGroups(CompletePattern $pattern, int|IntegerType $k): ShortestGroups
    {
        return new ShortestGroups($pattern, $k);
    }

    /**
     * Creates an ANY construct.
     *
     * @param CompletePattern $pattern The pattern to find any path for
     *
     * @see https://neo4j.com/docs/cypher-manual/current/patterns/shortest-paths/ Corresponding documentation on Neo4j.com
     */
    public static function anyPath(CompletePattern $pattern): AnyPath
    {
        return new AnyPath($pattern);
    }

    /**
     * Creates a new variable with the given name, or generates a new variable with a random unique name.
     *
     * @param null|string $variable the name of the variable, or null to automatically generate a name\
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/variables/ Corresponding documentation on Neo4j.com
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
     *  - Query::literal()::point2d(...) - For a 2D cartesian point
     *  - Query::literal()::point3d(...) - For a 3D cartesian point
     *  - Query::literal()::point2dWGS84(...) - For a 2D WGS 84 point
     *  - Query::literal()::point3dWGS84(...) - For a 3D WGS 84 point
     *
     * And a Date literal by using one of the following functions:
     *
     *  - Query::literal()::date(...) - For the current date
     *  - Query::literal()::dateYMD(...) - For a date from the given year, month and day
     *  - Query::literal()::dateYWD(...) - For a date from the given year, week and day
     *  - Query::literal()::dateString(...) - For a date from the given date string
     *  - Query::literal()::dateTime(...) - For the current datetime
     *  - Query::literal()::dateTimeYMD(...) - For a datetime from the given parameters (see function definition)
     *  - Query::literal()::dateTimeYWD(...) - For a datetime from the given parameters (see function definition)
     *  - Query::literal()::dateTimeYQD(...) - For a datetime from the given parameters (see function definition)
     *  - Query::literal()::dateTimeYD(...) - For a datetime from the given parameters (see function definition)
     *  - Query::literal()::dateTimeString(...) - For a datetime from the given datetime string
     *  - Query::literal()::localDateTime(...) - For the current local datetime
     *  - Query::literal()::localDateTimeYMD(...) - For a local datetime from the given parameters (see function definition)
     *  - Query::literal()::localDateTimeYWD(...) - For a local datetime from the given parameters (see function definition)
     *  - Query::literal()::localDateTimeYQD(...) - For a local datetime from the given parameters (see function definition)
     *  - Query::literal()::localDateTimeYD(...) - For a local datetime from the given parameters (see function definition)
     *  - Query::literal()::localDateTimeString(...) - For a local datetime from the given datetime string
     *  - Query::literal()::localTimeCurrent(...) - For the current LocalTime
     *  - Query::literal()::localTime(...) - For a local time from the given parameters (see function definition)
     *  - Query::literal()::localTimeString(...) - For a local time from the given time string
     *  - Query::literal()::time(...) - For the curren time
     *  - Query::literal()::timeHMS(...) - For a time from the given hour, minute and second
     *  - Query::literal()::timeString(...) - For a time from the given time string
     *
     * When no arguments are given to this function, the function will return a reference to the Literal class.
     *
     * You can directly call the constructors of the most basic types:
     *
     *  - Query::boolean() - For a boolean
     *  - Query::string() - For a string
     *  - Query::integer() - For an integer
     *  - Query::float() - For a float
     *  - Query::list() - For a list
     *  - Query::map() - For a map
     *
     * @param null|array|bool|float|int|string|Stringable $literal The literal to construct
     *
     * @return Boolean|class-string<Literal>|Float_|Integer|List_|Map|String_
     */
    // @phpstan-ignore-next-line
    public static function literal(null|bool|float|int|array|string|Stringable $literal = null): Boolean|Float_|Integer|List_|Map|String_|string
    {
        if ($literal === null) {
            return self::literal;
        }

        return Literal::literal($literal);
    }

    /**
     * Creates a new boolean.
     */
    public static function boolean(bool $value): Boolean
    {
        // @phpstan-ignore-next-line
        return self::literal()::boolean($value);
    }

    /**
     * Creates a new string.
     */
    public static function string(string $value): String_
    {
        // @phpstan-ignore-next-line
        return self::literal()::string($value);
    }

    /**
     * Creates a new integer.
     */
    public static function integer(int $value): Integer
    {
        // @phpstan-ignore-next-line
        return self::literal()::integer($value);
    }

    /**
     * Creates a new float.
     */
    public static function float(float $value): Float_
    {
        // @phpstan-ignore-next-line
        return self::literal()::float($value);
    }

    /**
     * Creates a new list literal.
     *
     * @param mixed[] $value
     */
    public static function list(iterable $value): List_
    {
        // @phpstan-ignore-next-line
        return self::literal()::list($value);
    }

    /**
     * Creates a new map literal.
     *
     * @param mixed[] $value
     */
    public static function map(array $value): Map
    {
        // @phpstan-ignore-next-line
        return self::literal()::map($value);
    }

    /**
     * Creates a new parameter.
     *
     * @param null|string $parameter The name of the parameter, or null to automatically generate a name
     */
    public static function parameter(?string $parameter = null): Parameter
    {
        return new Parameter($parameter);
    }

    /**
     * Returns the class string of the "Procedure" class. This can be used to more easily create new functions calls.
     *
     * @return class-string<Procedure>
     */
    public static function function(): string
    {
        return self::procedure();
    }

    /**
     * Returns the class string of the "Procedure" class. This can be used to more easily create new functions calls.
     *
     * @return class-string<Procedure>
     */
    public static function procedure(): string
    {
        return self::procedure;
    }

    /**
     * Creates a new raw expression.
     *
     * @note This should be used only for features that are not implemented by the DSL.
     *
     * @param string $expression The raw expression
     */
    public static function rawExpression(string $expression): RawExpression
    {
        return new RawExpression($expression);
    }

    /**
     * Creates an EXISTS expression.
     */
    public static function exists(CompletePattern|MatchClause|array $match, WhereClause|BooleanType|bool|null $where = null, bool $insertParentheses = false): Exists
    {
        if (!$match instanceof MatchClause) {
            $match = is_array($match) ? $match : [$match];
            $match = (new MatchClause())->addPattern(...$match);
        }

        if (!$where instanceof WhereClause && $where !== null) {
            $where = (new WhereClause())->addExpression($where);
        }

        return new Exists($match, $where);
    }

    /**
     * @see Query::new()
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct()
    {
        // This constructor currently does nothing, but we still define it, so we can mark it as internal. We could
        // make it private, but people might assume the class should not be constructed at all.
    }

    /**
     * Automatically build the query if this object is used as a string somewhere.
     */
    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * Creates a CALL sub query clause and adds it to the query.
     *
     * @note This feature is not part of the openCypher standard.
     *
     * @param callable|Query                                      $query     A callable decorating a Query, or an instance of Query
     * @param Pattern|(Pattern|string|Variable)[]|string|Variable $variables The variables to include in the WITH clause for correlation (optional)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call-subquery/ Corresponding documentation on Neo4j.com
     */
    public function call(self|callable $query, Pattern|Variable|string|array $variables = []): self
    {
        if (!is_array($variables)) {
            $variables = [$variables];
        }

        if (is_callable($query)) {
            $subQuery = self::new();
            $query($subQuery);
        } else {
            $subQuery = $query;
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
     * @param Procedure|string $procedure The procedure to call
     * @param mixed            $yields    The result fields that should be returned (optional)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/ Corresponding documentation on Neo4j.com
     */
    public function callProcedure(Procedure|string $procedure, mixed $yields = []): self
    {
        if (is_string($procedure)) {
            $procedure = Procedure::raw($procedure);
        }

        if (!is_array($yields)) {
            $yields = [$yields];
        }

        $yields = $this->makeAliasArray($yields, static fn ($value) => CastUtils::toName($value));

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure($procedure);
        $callProcedureClause->addYield(...$yields);

        $this->clauses[] = $callProcedureClause;

        return $this;
    }

    /**
     * Creates the MATCH clause.
     *
     * @param CompletePattern|CompletePattern[] $patterns A single pattern or a non-empty list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/ Corresponding documentation on Neo4j.com
     */
    public function match(CompletePattern|array $patterns): self
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
     * @param mixed $expressions A single expression to return, or a non-empty list of expressions to return
     * @param bool  $distinct    Whether to be a RETURN DISTINCT clause (optional, default: false)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/ Corresponding documentation on Neo4j.com
     */
    public function returning(mixed $expressions, bool $distinct = false): self
    {
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $expressions = $this->makeAliasArray($expressions, static fn ($value) => CastUtils::toAnyType($value));

        $returnClause = new ReturnClause();
        $returnClause->addColumn(...$expressions);
        $returnClause->setDistinct($distinct);

        $this->clauses[] = $returnClause;

        return $this;
    }

    /**
     * Creates the CREATE clause.
     *
     * @param CompletePattern|CompletePattern[] $patterns A single pattern or a non-empty list of patterns
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/create/ Corresponding documentation on Neo4j.com
     */
    public function create(CompletePattern|array $patterns): self
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
     * @param Pattern|(Pattern|StructuralType)[]|StructuralType $structures A single structure to delete, or a non-empty list of structures to delete
     * @param bool                                              $detach     Whether to DETACH DELETE (optional, default: false)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/ Corresponding documentation on Neo4j.com
     */
    public function delete(Pattern|StructuralType|array $structures, bool $detach = false): self
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
     * @param Pattern|(Pattern|StructuralType)[]|StructuralType $structures A single structure to delete, or a non-empty list of structures to delete
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/delete/ Corresponding documentation on Neo4j.com
     */
    public function detachDelete(Pattern|StructuralType|array $structures): self
    {
        return $this->delete($structures, true);
    }

    /**
     * Adds a LIMIT clause.
     *
     * @param int|IntegerType $limit The amount to use as the limit
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/limit/ Corresponding documentation on Neo4j.com
     */
    public function limit(IntegerType|int $limit): self
    {
        $limitClause = new LimitClause();
        $limitClause->setLimit($limit);

        $this->clauses[] = $limitClause;

        return $this;
    }

    /**
     * Creates the SKIP clause.
     *
     * @param int|IntegerType $amount The amount to skip
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/skip/ Corresponding documentation on Neo4j.com
     */
    public function skip(IntegerType|int $amount): self
    {
        $skipClause = new SkipClause();
        $skipClause->setSkip($amount);

        $this->clauses[] = $skipClause;

        return $this;
    }

    /**
     * Creates the MERGE clause.
     *
     * @param CompletePattern $pattern      The pattern to merge
     * @param null|SetClause  $createClause The clause to execute when the pattern is created (optional)
     * @param null|SetClause  $matchClause  The clause to execute when the pattern is matched (optional)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/merge/ Corresponding documentation on Neo4j.com
     */
    public function merge(CompletePattern $pattern, ?SetClause $createClause = null, ?SetClause $matchClause = null): self
    {
        $mergeClause = new MergeClause();
        $mergeClause->setPattern($pattern);
        $mergeClause->setOnCreate($createClause);
        $mergeClause->setOnMatch($matchClause);

        $this->clauses[] = $mergeClause;

        return $this;
    }

    /**
     * Creates the OPTIONAL MATCH clause.
     *
     * @param CompletePattern|CompletePattern[] $patterns A single pattern to match, or a non-empty list of patterns to match
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/ Corresponding documentation on Neo4j.com
     */
    public function optionalMatch(CompletePattern|array $patterns): self
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
     * @param Property|Property[] $properties A single property to order by, or a non-empty list of properties to order by
     * @param bool                $descending Whether to order in descending order (optional, default: false)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/order-by/ Corresponding documentation on Neo4j.com
     */
    public function orderBy(Property|array $properties, bool $descending = false): self
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
     * @param Label|(Label|Property)[]|Property $expressions A single expression to remove, or a non-empty list of expressions to remove
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/remove/ Corresponding documentation on Neo4j.com
     */
    public function remove(Label|Property|array $expressions): self
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
     * @param Label|(Label|PropertyReplacement)[]|PropertyReplacement $expressions A single expression to set, or a non-empty list of expressions to set
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/set/ Corresponding documentation on Neo4j.com
     */
    public function set(Label|PropertyReplacement|array $expressions): self
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
     * @param bool|BooleanType|(bool|BooleanType)[] $expressions A boolean expression to evaluate, or a non-empty list of boolean expression to evaluate
     * @param string                                $operator    The operator with which to unify the given expressions, should be either WhereClause::OR,
     *                                                           WhereClause::AND or WhereClause::XOR (optional, default: 'and')
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/ Corresponding documentation on Neo4j.com
     */
    public function where(BooleanType|bool|array $expressions, string $operator = WhereClause::AND): self
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
     * @param mixed $expressions An entry to add, or a non-empty list of entries to add; if the array-key is non-numerical, it is used as the alias
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/ Corresponding documentation on Neo4j.com
     */
    public function with(mixed $expressions): self
    {
        if (!is_array($expressions)) {
            $expressions = [$expressions];
        }

        $expressions = $this->makeAliasArray($expressions, static fn ($value) => CastUtils::toAnyType($value));

        $withClause = new WithClause();
        $withClause->addEntry(...$expressions);

        $this->clauses[] = $withClause;

        return $this;
    }

    /**
     * Creates a raw clause.
     *
     * @note This should only be used for features that are not implemented by the DSL.
     *
     * @param string $clause  The name of the clause, for instance "MATCH"
     * @param string $subject The subject (body) of the clause
     *
     * @return $this
     */
    public function raw(string $clause, string $subject): self
    {
        $this->clauses[] = new RawClause($clause, $subject);

        return $this;
    }

    /**
     * Combines the result of this query with another one via a UNION clause.
     *
     * @param callable|Query $queryOrCallable The callable decorating a fresh query instance or the query instance to be attached after the union clause
     * @param bool           $all             Whether the union should include all results or remove the duplicates instead (optional, default: false)
     *
     * @return $this
     *
     * @see https://neo4j.com/docs/cypher-manual/current/clauses/union/ Corresponding documentation on Neo4j.com
     */
    public function union(self|callable $queryOrCallable, bool $all = false): self
    {
        if (is_callable($queryOrCallable)) {
            $query = self::new();
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
     *
     * @return $this
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
            static fn (Clause $clause): string => $clause->toQuery(),
            $this->clauses
        );

        // Filter any empty clauses to prevent double spaces
        return implode(" ", array_filter($builtClauses, static fn ($clause) => !empty($clause)));
    }

    /**
     * Alias of $this->build().
     *
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return $this->build();
    }

    /**
     * Changes an associative array into an array of aliases.
     *
     * @param array    $values   The array to change into an array of aliases
     * @param callable $castFunc Function to use to cast the elements of $array
     *
     * @return array A sequential array, possibly consisting of aliases
     */
    private static function makeAliasArray(array $values, callable $castFunc): array
    {
        $res = [];

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                /**
                 * @var AnyType $value
                 */
                $value = $castFunc($value);
                $res[] = $value->alias($key);
            } else {
                // If key is numeric, keep value unchanged.
                $res[]= $value;
            }
        }

        return $res;
    }
}
