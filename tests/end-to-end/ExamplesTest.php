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
use function WikibaseSolutions\CypherDSL\integer;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\procedure;
use WikibaseSolutions\CypherDSL\Query;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\raw;
use function WikibaseSolutions\CypherDSL\relationshipFrom;
use function WikibaseSolutions\CypherDSL\relationshipTo;
use function WikibaseSolutions\CypherDSL\relationshipUni;
use function WikibaseSolutions\CypherDSL\variable;

/**
 * This class contains some end-to-end tests to test the examples in the wiki.
 *
 * @coversNothing
 *
 * @see https://github.com/neo4j-php/php-cypher-dsl/wiki
 */
final class ExamplesTest extends TestCase
{
    public function testOldReadmeExample(): void
    {
        $tom = node("Person")->withProperties(["name" => "Tom Hanks"]);
        $coActors = node();

        $statement = query()
            ->match($tom->relationshipTo(node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
            ->returning($coActors->property("name"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Tom Hanks'})-[:ACTED_IN]->()<-[:ACTED_IN]-(%s) RETURN %s.name", $statement);
    }

    public function testCallSubqueryClauseExample1(): void
    {
        $query = query()
            ->call(static function (Query $query): void {
                $query->create(node("Person"));
            })
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample2(): void
    {
        $subQuery = query()->create(node("Person"));
        $query = query()
            ->call($subQuery)
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample3(): void
    {
        $person = variable();
        $query = query()
            ->match(node('Person')->withVariable($person))
            ->call(static function (Query $query) use ($person): void {
                $query->remove($person->labeled('Person'));
            }, [$person])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) CALL { WITH %s REMOVE %s:Person }", $query);
    }

    public function testCallProcedureClauseExample1(): void
    {
        $statement = query()
            ->callProcedure("apoc.json")
            ->build();

        $this->assertSame("CALL `apoc.json`()", $statement);
    }

    public function testCallProcedureClauseExample2(): void
    {
        $statement = query()
            ->callProcedure("dbms.procedures", [
                variable('name'),
                variable('signature'),
            ])
            ->build();

        $this->assertSame("CALL `dbms.procedures`() YIELD name, signature", $statement);
    }

    public function testCallProcedureClauseExample3(): void
    {
        $procedure = Procedure::raw("dbms.security.createUser", ['example_username', 'example_password', false]);
        $statement = query()
            ->callProcedure($procedure)
            ->build();

        $this->assertSame("CALL `dbms.security.createUser`('example_username', 'example_password', false)", $statement);
    }

    public function testCreateClauseExample1(): void
    {
        $query = query()
            ->create(node("Person"))
            ->build();

        $this->assertSame("CREATE (:Person)", $query);
    }

    public function testCreateClauseExample2(): void
    {
        $query = query()
            ->create(node("Person")->withVariable('n')->withProperties([
                'name' => 'Marijn',
                'title' => 'Maintainer',
            ]))
            ->build();

        $this->assertSame("CREATE (n:Person {name: 'Marijn', title: 'Maintainer'})", $query);
    }

    public function testCreateClauseExample3(): void
    {
        $query = query()
            ->create([node("Person"), node("Animal")])
            ->build();

        $this->assertSame("CREATE (:Person), (:Animal)", $query);
    }

    public function testDeleteClauseExample1(): void
    {
        $unknown = node('Person')->withProperties([
            'name' => 'UNKNOWN',
        ]);

        $query = query()
            ->match($unknown)
            ->delete($unknown)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person {name: 'UNKNOWN'}) DELETE %s", $query);
    }

    public function testDeleteClauseExample2(): void
    {
        $everything = node();

        $query = query()
            ->match($everything)
            ->delete($everything)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DELETE %s", $query);
    }

    public function testDeleteClauseExample3(): void
    {
        $everything = node();

        $query = query()
            ->match($everything)
            ->delete($everything, true)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DETACH DELETE %s", $query);
    }

    public function testDeleteClauseExample4(): void
    {
        $everything = node();

        $query = query()
            ->match($everything)
            ->detachDelete($everything)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) DETACH DELETE %s", $query);
    }

    public function testDeleteClauseExample5(): void
    {
        $persons = node('Person');
        $animals = node('Animal');

        $query = query()
            ->match([$persons, $animals])
            ->delete([$persons, $animals])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person), (%s:Animal) DELETE %s, %s", $query);
    }

    public function testLimitClauseExample1(): void
    {
        $persons = node('Person');
        $query = query()
            ->match($persons)
            ->returning($persons->property('name'))
            ->limit(3)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) RETURN %s.name LIMIT 3", $query);
    }

    public function testMatchClauseExample1(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning($n)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s", $query);
    }

    public function testCreateReuseVariable(): void
    {
        $charlie = node("Person")->withProperties(["name" => "Charlie Sheen"]);
        $oliver = node("Person")->withProperties(["name" => "Oliver Stone"]);

        $wallStreet = node("Movie")->withProperties(["title" => "Wall Street"]);

        $query = query()
            ->match([$charlie, $oliver])
            ->create($charlie->variable()->relationshipTo($wallStreet, type: "ACTED_IN", properties: ["role" => "Bud Fox"])->relationshipFrom($oliver->variable()))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person {name: 'Charlie Sheen'}), (%s:Person {name: 'Oliver Stone'}) CREATE (%s)-[:ACTED_IN {role: 'Bud Fox'}]->(:Movie {title: 'Wall Street'})<--(%s)", $query);
    }

    public function testMatchClauseExample2(): void
    {
        $movie = node("Movie");
        $query = query()
            ->match($movie)
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample3(): void
    {
        $movie = node();
        $query = query()
            ->match(node()->withProperties(['name' => 'Oliver Stone'])->relationshipUni($movie))
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Oliver Stone'})--(%s) RETURN %s.title", $query);
    }

    public function testMatchClauseExample4(): void
    {
        $movie = node('Movie');
        $query = query()
            ->match(node('Person')->withProperties(['name' => 'Oliver Stone'])->relationshipUni($movie))
            ->returning($movie->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})--(%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample5(): void
    {
        $n = node()->addLabel('Movie', 'Person');
        $query = query()
            ->match($n)
            ->returning(['name' => $n->property("name"), 'title' => $n->property("title")])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie:Person) RETURN %s.name AS name, %s.title AS title", $query);
    }

    public function testMatchClauseExample6(): void
    {
        $movie = node();
        $query = query()
            ->match(
                node('Person')->withProperties(['name' => 'Oliver Stone'])->relationshipTo($movie)
            )
            ->returning($movie->property('title'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})-->(%s) RETURN %s.title", $query);
    }

    public function testMatchClauseExample7(): void
    {
        $r = relationshipTo();
        $query = query()
            ->match(node('Person')->withProperties(['name' => 'Oliver Stone'])->relationship($r, node()))
            ->returning($r)
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Oliver Stone'})-[%s]->() RETURN %s", $query);
    }

    public function testMatchClauseExample8(): void
    {
        $actor = node();
        $query = query()
            ->match(
                node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationshipFrom($actor, 'ACTED_IN')
            )
            ->returning($actor->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[:ACTED_IN]-(%s) RETURN %s.name", $query);
    }

    public function testMatchClauseExample9(): void
    {
        $person = node();
        $relationship = relationshipFrom()->withTypes(['ACTED_IN', 'DIRECTED']);
        $query = query()
            ->match(
                node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationship($relationship, $person)
            )
            ->returning($person->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[:ACTED_IN|DIRECTED]-(%s) RETURN %s.name", $query);
    }

    public function testMatchClauseExample10(): void
    {
        $actor = node();
        $relationship = relationshipFrom()->withTypes(['ACTED_IN']);
        $query = query()
            ->match(
                node('Movie')
                    ->withProperties(['title' => 'Wall Street'])
                    ->relationship($relationship, $actor)
            )
            ->returning($relationship->property('role'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Movie {title: 'Wall Street'})<-[%s:ACTED_IN]-() RETURN %s.role", $query);
    }

    public function testMatchClauseExample11(): void
    {
        $charlie = node('Person')->withProperties(['name' => 'Charlie Sheen']);
        $rob = node('Person')->withProperties(['name' => 'Rob Reiner']);

        $query = query()
            ->match([$charlie, $rob])
            ->create(
                node()
                    ->withVariable($rob->getVariable())
                    ->relationshipTo(
                        node()->withVariable($charlie->getVariable()),
                        'TYPE INCLUDING A SPACE'
                    )
            )
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person {name: 'Charlie Sheen'}), (%s:Person {name: 'Rob Reiner'}) CREATE (%s)-[:`TYPE INCLUDING A SPACE`]->(%s)", $query);
    }

    public function testMatchClauseExample12(): void
    {
        $movie = node();
        $director = node();

        $query = query()
            ->match(
                node()
                    ->withProperties(['name' => 'Charlie Sheen'])
                    ->relationshipTo($movie, 'ACTED_IN')
                    ->relationshipFrom($director, 'DIRECTED')
            )
            ->returning([$movie->property('title'), $director->property('name')])
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Charlie Sheen'})-[:ACTED_IN]->(%s)<-[:DIRECTED]-(%s) RETURN %s.title, %s.name", $query);
    }

    public function testMatchClauseExample13(): void
    {
        $movie = node('Movie');
        $r = relationshipUni()->addType('ACTED_IN')->withMinHops(1)->withMaxHops(3);

        $query = query()
            ->match(node()->withProperties(['name' => 'Charlie Sheen'])->relationship($r, $movie))
            ->returning($movie->property('title'))
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'Charlie Sheen'})-[:ACTED_IN*1..3]-(%s:Movie) RETURN %s.title", $query);
    }

    public function testMatchClauseExample14(): void
    {
        $p = node()->withProperties(['name' => 'Michael Douglas'])->relationshipTo(node());

        $query = query()
            ->match($p)
            ->returning($p)
            ->build();

        $this->assertStringMatchesFormat("MATCH %s = ({name: 'Michael Douglas'})-->() RETURN %s", $query);
    }

    public function testMergeClauseExample1(): void
    {
        $robert = node('Critic');

        $query = query()
            ->merge($robert)
            ->returning($robert)
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Critic) RETURN %s", $query);
    }

    public function testMergeClauseExample2(): void
    {
        $keanu = node('Person')->withProperties(['name' => 'Keanu Reeves']);

        $query = query()
            ->merge($keanu, (new SetClause())->add($keanu->property('created')->replaceWith(procedure()::raw('timestamp'))))
            ->returning([$keanu->property('name'), $keanu->property('created')])
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Person {name: 'Keanu Reeves'}) ON CREATE SET %s.created = timestamp() RETURN %s.name, %s.created", $query);
    }

    public function testMergeClauseExample3(): void
    {
        $keanu = node('Person')->withProperties(['name' => 'Keanu Reeves']);

        $query = query()
            ->merge($keanu, null, (new SetClause())->add($keanu->property('created')->replaceWith(procedure()::raw('timestamp'))))
            ->returning([$keanu->property('name'), $keanu->property('created')])
            ->build();

        $this->assertStringMatchesFormat("MERGE (%s:Person {name: 'Keanu Reeves'}) ON MATCH SET %s.created = timestamp() RETURN %s.name, %s.created", $query);
    }

    public function testOptionalMatchClauseExample1(): void
    {
        $movies = node("Movie");
        $query = query()
            ->optionalMatch($movies)
            ->build();

        $this->assertSame("OPTIONAL MATCH (:Movie)", $query);
    }

    public function testOrderByClauseExample1(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning([$n->property('name'), $n->property('age')])
            ->orderBy($n->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name, %s.age ORDER BY %s.name", $query);
    }

    public function testOrderByClauseExample2(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning([$n->property('name'), $n->property('age')])
            ->orderBy([$n->property('age'), $n->property('name')])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name, %s.age ORDER BY %s.age, %s.name", $query);
    }

    public function testOrderByClauseExample3(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning([$n->property('name'), $n->property('age')])
            ->orderBy([$n->property('name')], true)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name, %s.age ORDER BY %s.name DESCENDING", $query);
    }

    public function testRemoveClauseExample1(): void
    {
        $a = node()->withProperties(['name' => 'Andy']);
        $query = query()
            ->match($a)
            ->remove($a->property('age'))
            ->returning([$a->property('name'), $a->property('age')])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'Andy'}) REMOVE %s.age RETURN %s.name, %s.age", $query);
    }

    public function testRemoveClauseExample2(): void
    {
        $n = node()->withProperties(['name' => 'Peter']);
        $query = query()
            ->match($n)
            ->remove($n->labeled('German'))
            ->returning($n->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'Peter'}) REMOVE %s:German RETURN %s.name", $query);
    }

    public function testRemoveClauseExample3(): void
    {
        $n = node()->withProperties(['name' => 'Peter']);
        $query = query()
            ->match($n)
            ->remove($n->labeled('German', 'Swedish'))
            ->returning($n->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'Peter'}) REMOVE %s:German:Swedish RETURN %s.name", $query);
    }

    public function testReturnClauseExample1(): void
    {
        $n = node()->withProperties(['name' => 'B']);
        $query = query()
            ->match($n)
            ->returning($n)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'B'}) RETURN %s", $query);
    }

    public function testReturnClauseExample2(): void
    {
        $r = relationshipTo()->addType('KNOWS');
        $query = query()
            ->match(node()->withProperties(['name' => 'A'])->relationship($r, node()))
            ->returning($r)
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'A'})-[%s:KNOWS]->() RETURN %s", $query);
    }

    public function testReturnClauseExample3(): void
    {
        $n = node()->withProperties(['name' => 'A']);
        $query = query()
            ->match($n)
            ->returning($n->property('name'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'A'}) RETURN %s.name", $query);
    }

    public function testReturnClauseExample4(): void
    {
        $n = node()->withVariable('This isn\'t a common variable name');
        $query = query()
            ->match($n)
            ->where($n->property('name')->equals('A'))
            ->returning($n->property('happy'))
            ->build();

        $this->assertSame("MATCH (`This isn't a common variable name`) WHERE `This isn't a common variable name`.name = 'A' RETURN `This isn't a common variable name`.happy", $query);
    }

    public function testReturnClauseExample5(): void
    {
        $a = node()->withProperties(['name' => 'A']);
        $query = query()
            ->match($a)
            ->returning($a->property('age')->alias('SomethingTotallyDifferent'))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'A'}) RETURN %s.age AS SomethingTotallyDifferent", $query);
    }

    public function testReturnClauseExample6(): void
    {
        $a = node()->withProperties(['name' => 'A']);
        $query = query()
            ->match($a)
            ->returning([$a->property('age')->gt(30), "I'm a literal"])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'A'}) RETURN %s.age > 30, 'I\\'m a literal'", $query);
    }

    public function testReturnClauseExample7(): void
    {
        $b = node();
        $query = query()
            ->match(node()->withProperties(['name' => 'A'])->relationshipTo($b))
            ->returning($b, true)
            ->build();

        $this->assertStringMatchesFormat("MATCH ({name: 'A'})-->(%s) RETURN DISTINCT %s", $query);
    }

    public function testSetClauseExample1(): void
    {
        $n = node()->withProperties(['name' => 'Andy']);
        $query = query()
            ->match($n)
            ->set($n->property('surname')->replaceWith('Taylor'))
            ->returning([$n->property('name'), $n->property('surname')])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s {name: 'Andy'}) SET %s.surname = 'Taylor' RETURN %s.name, %s.surname", $query);
    }

    public function testUnionClauseExample1(): void
    {
        $actor = node('Actor');
        $movie = node('Movie');

        $query = query()
            ->match($actor)
            ->returning($actor->property('name')->alias('name'))
            ->union(
                query()
                    ->match($movie)
                    ->returning($movie->property('title')->alias('name')),
                true
            )
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Actor) RETURN %s.name AS name UNION ALL MATCH (%s:Movie) RETURN %s.title AS name", $query);
    }

    public function testUnionClauseExample2(): void
    {
        $actor = node('Actor');
        $movie = node('Movie');

        $query = query()
            ->match($actor)
            ->returning($actor->property('name')->alias('name'))
            ->union(
                query()
                    ->match($movie)
                    ->returning($movie->property('title')->alias('name'))
            )
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Actor) RETURN %s.name AS name UNION MATCH (%s:Movie) RETURN %s.title AS name", $query);
    }

    public function testSkipClauseExample1(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning($n->property('name'))
            ->orderBy($n->property('name'))
            ->skip(3)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name ORDER BY %s.name SKIP 3", $query);
    }

    public function testSkipClauseExample2(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning($n->property('name'))
            ->orderBy($n->property('name'))
            ->skip(1)
            ->limit(2)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name ORDER BY %s.name SKIP 1 LIMIT 2", $query);
    }

    public function testSkipClauseExample3(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->returning($n->property('name'))
            ->orderBy($n->property('name'))
            ->skip(integer(5)->exponentiate(2))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) RETURN %s.name ORDER BY %s.name SKIP 5 ^ 2", $query);
    }

    public function testWhereClauseExample1(): void
    {
        $n = node('Person');
        $query = query()
            ->match($n)
            ->where($n->property('name')->equals('Peter'))
            ->returning($n)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) WHERE %s.name = 'Peter' RETURN %s", $query);
    }

    public function testWhereClauseExample2(): void
    {
        $n = node();
        $query = query()
            ->match($n)
            ->where($n->labeled('Person'))
            ->returning($n)
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s) WHERE %s:Person RETURN %s", $query);
    }

    public function testCombiningClausesExample1(): void
    {
        $nineties = node("Movie");
        $expression = $nineties->property('released')->gte(1990)->and($nineties->property('released')->lt(2000));

        $statement = query()
            ->match($nineties)
            ->where($expression)
            ->returning($nineties->property("title"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Movie) WHERE %s.released >= 1990 AND %s.released < 2000 RETURN %s.title", $statement);
    }

    public function testExpressions1(): void
    {
        $released = variable("nineties")->property("released");
        $expression = $released->gte(1990)->and($released->lt(2000));

        $this->assertSame("nineties.released >= 1990 AND nineties.released < 2000", $expression->toQuery());
    }

    public function testExpressions2(): void
    {
        $name = variable("actor")->property("name");
        $expression = $name->notEquals("Tom Hanks");

        $this->assertSame("actor.name <> 'Tom Hanks'", $expression->toQuery());
    }

    public function testExpressions3(): void
    {
        $released = variable("nineties")->property("released");
        $expression = $released->gte(1990)->and(raw("(nineties IS NOT NULL)"));

        $this->assertSame("nineties.released >= 1990 AND (nineties IS NOT NULL)", $expression->toQuery());
    }
}
