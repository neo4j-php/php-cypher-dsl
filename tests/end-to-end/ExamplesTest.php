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

    public function testOptionalMatchClauseExample1(): void
    {
        $movies = Query::node("Movie");
        $query = Query::new()
            ->optionalMatch($movies)
            ->build();

        $this->assertSame("OPTIONAL MATCH (:Movie)", $query);
    }
}
