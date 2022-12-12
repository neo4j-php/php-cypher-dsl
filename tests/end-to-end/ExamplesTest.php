<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Query;

/**
 * This class contains some end-to-end tests to test the examples in the wiki.
 *
 * @coversNothing
 *
 * @see https://github.com/neo4j-php/php-cypher-dsl/wiki
 */
final class ExamplesTest extends TestCase
{
    public function testReadmeExample(): void
    {
        $tom = Query::node("Person")->withProperties(["name" => "Tom Hanks"]);
        $coActors = Query::node();

        $statement = Query::new()
            ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
            ->returning($coActors->property("name"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Tom Hanks'})-[:ACTED_IN]->()<-[:ACTED_IN]-(%s) RETURN %s.name", $statement);
    }

    public function testCallSubqueryClauseExample1(): void
    {
        $query = Query::new()
            ->call(static function (Query $query): void
            {
                $query->create(Query::node("Person"));
            })
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample2(): void
    {
        $subQuery = Query::new()->create(Query::node("Person"));
        $query = Query::new()
            ->call($subQuery)
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample3(): void
    {
        $person = Query::variable();
        $query = Query::new()
            ->match(Query::node('Person')->withVariable($person))
            ->call(static function (Query $query) use ($person): void
            {
                $query->remove($person->labeled('Person'));
            }, [$person])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) CALL { WITH %s REMOVE %s:Person }", $query);
    }

    public function testCallProcedureClauseExample1(): void
    {
        $statement = Query::new()
            ->callProcedure("apoc.json")
            ->build();

        $this->assertSame("CALL `apoc.json`()", $statement);
    }

    public function testCallProcedureClauseExample2(): void
    {
        $statement = Query::new()
            ->callProcedure("dbms.procedures", [
                Query::variable('name'),
                Query::variable('signature')
            ])
            ->build();

        $this->assertSame("CALL `dbms.procedures`() YIELD name, signature", $statement);
    }

    public function testCallProcedureClauseExample3(): void
    {
        $procedure = Procedure::raw("dbms.security.createUser", ['example_username', 'example_password', false]);
        $statement = Query::new()
            ->callProcedure($procedure)
            ->build();

        $this->assertSame("CALL `dbms.security.createUser`('example_username', 'example_password', false)", $statement);
    }

    public function testCreateClauseExample1(): void
    {
        $query = Query::new()
            ->create(Query::node("Person"))
            ->build();

        $this->assertSame("CREATE (:Person)", $query);
    }

    public function testCreateClauseExample2(): void
    {
        $query = Query::new()
            ->create(Query::node("Person")->withVariable('n')->withProperties([
                'name' => 'Marijn',
                'title' => 'Maintainer'
            ]))
            ->build();

        $this->assertSame("CREATE (n:Person {name: 'Marijn', title: 'Maintainer'})", $query);
    }

    public function testCreateClauseExample3(): void
    {
        $query = Query::new()
            ->create([Query::node("Person"), Query::node("Animal")])
            ->build();

        $this->assertSame("CREATE (:Person), (:Animal)", $query);
    }

    public function testDeleteClauseExample1(): void
    {
        $unknown = Query::node('Person')->withProperties([
            'name' => 'UNKNOWN'
        ]);

        $query = Query::new()
            ->match($unknown)
            ->delete($unknown)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person {name: 'UNKNOWN'}) DELETE %s", $query);
    }

    public function testDeleteClauseExample2(): void
    {
        $everything = Query::node();

        $query = Query::new()
            ->match($everything)
            ->delete($everything)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DELETE %s", $query);
    }

    public function testDeleteClauseExample3(): void
    {
        $everything = Query::node();

        $query = Query::new()
            ->match($everything)
            ->delete($everything, true)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DETACH DELETE %s", $query);
    }

    public function testDeleteClauseExample4(): void
    {
        $everything = Query::node();

        $query = Query::new()
            ->match($everything)
            ->detachDelete($everything)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DETACH DELETE %s", $query);
    }

    public function testDeleteClauseExample5(): void
    {
        $persons = Query::node('Person');
        $animals = Query::node('Animal');

        $query = Query::new()
            ->match([$persons, $animals])
            ->delete([$persons, $animals])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person), (%s:Animal) DELETE %s, %s", $query);
    }

    public function testLimitClauseExample1(): void
    {
        $persons = Query::node('Person');
        $query = Query::new()
            ->match($persons)
            ->returning($persons->property('name'))
            ->limit(3)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) RETURN %s.name LIMIT 3", $query);
    }

    public function testMatchClauseExample1(): void
    {
        $n = Query::node();
        $query = Query::new()
            ->match($n)
            ->returning($n)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s", $query);
    }

    public function testMatchClauseExample2(): void
    {
        $movie = Query::node("Movie");
        $query = Query::new()
            ->match($movie)
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample3(): void
    {
        $movie = Query::node();
        $query = Query::new()
            ->match(Query::node()->withProperties(['name' => 'Oliver Stone'])->relationshipUni($movie))
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Oliver Stone'})--(%s) RETURN %s.title", $query);
}

    public function testMatchClauseExample4(): void
    {
        $movie = Query::node('Movie');
        $query = Query::new()
            ->match(Query::node('Person')->withProperties(['name' => 'Oliver Stone'])->relationshipUni($movie))
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})--(%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample5(): void
    {
        $n = Query::node()->addLabel('Movie', 'Person');
        $query = Query::new()
            ->match($n)
            ->returning(['name' => $n->property("name"), 'title' => $n->property("title")])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie:Person) RETURN %s.name AS name, %s.title AS title", $query);
    }

    public function testMatchClauseExample6(): void
    {
        $movie = Query::node();
        $query = Query::new()
            ->match(
                Query::node('Person')->withProperties(['name' => 'Oliver Stone'])->relationshipTo($movie)
            )
            ->returning($movie->property('title'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})-->(%s) RETURN %s.title", $query);
    }

    public function testMatchClauseExample7(): void
    {
        $r = Query::relationshipTo();
        $query = Query::new()
            ->match(Query::node('Person')->withProperties(['name' => 'Oliver Stone'])->relationship($r, Query::node()))
            ->returning($r)
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})-[%s]->() RETURN %s", $query);
    }

    public function testMatchClauseExample8(): void
    {
        $actor = Query::node();
        $query = Query::new()
            ->match(
                Query::node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationshipFrom($actor, 'ACTED_IN')
            )
            ->returning($actor->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[:ACTED_IN]-(%s) RETURN %s.name", $query);
    }

    public function testMatchClauseExample9(): void
    {
        $person = Query::node();
        $relationship = Query::relationshipFrom()->withTypes(['ACTED_IN', 'DIRECTED']);
        $query = Query::new()
            ->match(
                Query::node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationship($relationship, $person)
            )
            ->returning($person->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[:ACTED_IN|DIRECTED]-(%s) RETURN %s.name", $query);
    }

    public function testMatchClauseExample10(): void
    {
        $actor = Query::node();
        $relationship = Query::relationshipFrom()->withTypes(['ACTED_IN']);
        $query = Query::new()
            ->match(
                Query::node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationship($relationship, $actor)
            )
            ->returning($relationship->property('role'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[%s:ACTED_IN]-() RETURN %s.role", $query);
    }

    public function testMatchClauseExample11(): void
    {
        $charlie = Query::node('Person')->withProperties(['name' => 'Charlie Sheen']);
        $rob = Query::node('Person')->withProperties(['name' => 'Rob Reiner']);

        $query = Query::new()
            ->match([$charlie, $rob])
            ->create(
                Query::node()
                    ->withVariable($rob->getVariable())
                    ->relationshipTo(
                        Query::node()->withVariable($charlie->getVariable()), 'TYPE INCLUDING A SPACE')
                )
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person {name: 'Charlie Sheen'}), (%s:Person {name: 'Rob Reiner'}) CREATE (%s)-[:`TYPE INCLUDING A SPACE`]->(%s)", $query);
    }

    public function testMatchClauseExample12(): void
    {
        $movie = Query::node();
        $director = Query::node();

        $query = Query::new()
            ->match(
                Query::node()
                    ->withProperties(['name' => 'Charlie Sheen'])
                    ->relationshipTo($movie, 'ACTED_IN')
                    ->relationshipFrom($director, 'DIRECTED'))
            ->returning([$movie->property('title'), $director->property('name')])
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Charlie Sheen'})-[:ACTED_IN]->(%s)<-[:DIRECTED]-(%s) RETURN %s.title, %s.name", $query);
    }

    public function testMatchClauseExample13(): void
    {
        $movie = Query::node('Movie');
        $r = Query::relationshipUni()->addType('ACTED_IN')->withMinHops(1)->withMaxHops(3);

        $query = Query::new()
            ->match(Query::node()->withProperties(['name' => 'Charlie Sheen'])->relationship($r, $movie))
            ->returning($movie->property('title'))
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Charlie Sheen'})-[:ACTED_IN*1..3]-(%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample14(): void
    {
        $p = Query::node()->withProperties(['name' => 'Michael Douglas'])->relationshipTo(Query::node());

        $query = Query::new()
            ->match($p)
            ->returning($p)
            ->build();

        $this->assertStringMatchesFormat("MATCH %s = ({name: 'Michael Douglas'})-->() RETURN %s", $query);
    }

    public function testMergeClauseExample1(): void
    {
        $robert = Query::node('Critic');

        $query = Query::new()
            ->merge($robert)
            ->returning($robert)
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Critic) RETURN %s", $query);
    }

    public function testMergeClauseExample2(): void
    {
        $keanu = Query::node('Person')->withProperties(['name' => 'Keanu Reeves']);

        $query = Query::new()
            ->merge($keanu, (new SetClause())->add($keanu->property('created')->replaceWith(Query::procedure()::raw('timestamp'))))
            ->returning([$keanu->property('name'), $keanu->property('created')])
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Person {name: 'Keanu Reeves'}) ON CREATE SET %s.created = timestamp() RETURN %s.name, %s.created", $query);
    }

    public function testMergeClauseExample3(): void
    {
        $keanu = Query::node('Person')->withProperties(['name' => 'Keanu Reeves']);

        $query = Query::new()
            ->merge($keanu, null, (new SetClause())->add($keanu->property('created')->replaceWith(Query::procedure()::raw('timestamp'))))
            ->returning([$keanu->property('name'), $keanu->property('created')])
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Person {name: 'Keanu Reeves'}) ON MATCH SET %s.created = timestamp() RETURN %s.name, %s.created", $query);
    }

    public function testOptionalMatchClauseExample1(): void
    {
        $movies = Query::node("Movie");
        $query = Query::new()
            ->optionalMatch($movies)
            ->build();

        $this->assertSame("OPTIONAL MATCH (:Movie)", $query);
    }

    public function testCombiningClausesExample1(): void
    {
        $nineties = Query::node("Movie");
        $expression = $nineties->property('released')->gte(1990)->and($nineties->property('released')->lt(2000));

        $statement = Query::new()
            ->match($nineties)
            ->where($expression)
            ->returning($nineties->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie) WHERE ((%s.released >= 1990) AND (%s.released < 2000)) RETURN %s.title", $statement);
    }

    public function testExpressions1(): void
    {
        $released = Query::variable("nineties")->property("released");
        $expression = $released->gte(1990)->and($released->lt(2000));

        $this->assertSame("((nineties.released >= 1990) AND (nineties.released < 2000))", $expression->toQuery());
    }

    public function testExpressions2(): void
    {
        $name = Query::variable("actor")->property("name");
        $expression = $name->notEquals("Tom Hanks");

        $this->assertSame("(actor.name <> 'Tom Hanks')", $expression->toQuery());
    }

    public function testExpressions3(): void
    {
        $released = Query::variable("nineties")->property("released");
        $expression = $released->gte(1990)->and(Query::rawExpression("(nineties IS NOT NULL)"));

        $this->assertSame("((nineties.released >= 1990) AND (nineties IS NOT NULL))", $expression->toQuery());
    }
}
