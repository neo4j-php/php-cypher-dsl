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
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipUni;

/**
 * This class contains some end-to-end tests to test the creation of Cypher queries present in the ":play movies"
 * example.
 *
 * @coversNothing
 *
 * @see https://neo4j.com/developer/example-data/
 */
final class MoviesTest extends TestCase
{
    public function testFindActorNamedTomHanks(): void
    {
        $tom = node()->withProperties([
            'name' => 'Tom Hanks',
        ]);

        $query = query()
            ->match($tom)
            ->returning($tom);

        $this->assertStringMatchesFormat('MATCH (%s {name: \'Tom Hanks\'}) RETURN %s', $query->toQuery());
    }

    public function testFindTheMovieWithTitleCloudAtlas(): void
    {
        $cloudAtlas = node()->withProperties([
            'title' => 'Cloud Atlas',
        ]);

        $query = query()
            ->match($cloudAtlas)
            ->returning($cloudAtlas);

        $this->assertStringMatchesFormat('MATCH (%s {title: \'Cloud Atlas\'}) RETURN %s', $query->toQuery());
    }

    public function testFind10People(): void
    {
        $people = node('Person');

        $query = query()
            ->match($people)
            ->returning($people->property('name'))
            ->limit(10);

        $this->assertStringMatchesFormat('MATCH (%s:Person) RETURN %s.name LIMIT 10', $query->toQuery());
    }

    public function testFindMoviesReleasedInThe1990s(): void
    {
        $nineties = node('Movie');
        $query = query()
            ->match($nineties)
            ->where([
                $nineties->property('released')->gte(1990),
                $nineties->property('released')->lt(2000),
            ])
            ->returning($nineties->property('title'));

        $this->assertStringMatchesFormat('MATCH (%s:Movie) WHERE ((%s.released >= 1990) AND (%s.released < 2000)) RETURN %s.title', $query->toQuery());
    }

    public function testListAllTomHanksMovies(): void
    {
        $movies = node();
        $tom = node('Person')->withProperties([
            'name' => 'Tom Hanks',
        ]);

        $query = query()
            ->match($tom->relationshipTo($movies, 'ACTED_IN'))
            ->returning([$tom, $movies]);

        $this->assertStringMatchesFormat('MATCH (%s:Person {name: \'Tom Hanks\'})-[:ACTED_IN]->(%s) RETURN %s, %s', $query->toQuery());
    }

    public function testWhoDirectedCloudAtlas(): void
    {
        $directors = node();
        $cloudAtlas = node()->withProperties([
            'title' => 'Cloud Atlas',
        ]);

        $query = query()
            ->match($cloudAtlas->relationshipFrom($directors, 'DIRECTED'))
            ->returning($directors->property('name'));

        $this->assertStringMatchesFormat('MATCH ({title: \'Cloud Atlas\'})<-[:DIRECTED]-(%s) RETURN %s.name', $query->toQuery());
    }

    public function testTomHanksCoActors(): void
    {
        $coActors = node();
        $tom = node('Person')->withProperties([
            'name' => 'Tom Hanks',
        ]);

        $query = query()
            ->match($tom->relationshipTo(node(), 'ACTED_IN')->relationshipFrom($coActors, 'ACTED_IN'))
            ->returning($coActors->property('name'));

        $this->assertStringMatchesFormat('MATCH (:Person {name: \'Tom Hanks\'})-[:ACTED_IN]->()<-[:ACTED_IN]-(%s) RETURN %s.name', $query->toQuery());
    }

    public function testMoviesAndActorsUpTo4HopsAwayFromKevinBacon(): void
    {
        $hollywood = node();
        $bacon = node('Person')->withProperties([
            'name' => 'Kevin Bacon',
        ]);

        $relation = relationshipUni()
            ->withMinHops(1)
            ->withMaxHops(4);

        $query = query()
            ->match($bacon->relationship($relation, $hollywood))
            ->returning($hollywood, true);

        $this->assertStringMatchesFormat('MATCH (:Person {name: \'Kevin Bacon\'})-[*1..4]-(%s) RETURN DISTINCT %s', $query->toQuery());
    }

    public function testFindSomeoneToIntroduceTomHanksToTomCruise(): void
    {
        $tom = node('Person')->withProperties([
            'name' => 'Tom Hanks',
        ]);

        $cruise = node('Person')->withProperties([
            'name' => 'Tom Cruise',
        ]);

        $m = node();
        $m2 = node();
        $coActors = node();

        $query = query()
            ->match([
                $tom->relationshipTo($m, 'ACTED_IN')->relationshipFrom($coActors, 'ACTED_IN'),
                $coActors->relationshipTo($m2, 'ACTED_IN')->relationshipFrom($cruise, 'ACTED_IN'),
            ])
            ->returning([$tom, $m, $coActors, $m2, $cruise]);

        $this->assertStringMatchesFormat('MATCH (%s:Person {name: \'Tom Hanks\'})-[:ACTED_IN]->(%s)<-[:ACTED_IN]-(%s), (%s)-[:ACTED_IN]->(%s)<-[:ACTED_IN]-(%s:Person {name: \'Tom Cruise\'}) RETURN %s, %s, %s, %s', $query->toQuery());
    }

    public function testDeleteAllMovieAndPersonNodes(): void
    {
        $n = node();

        $query = query()
            ->match($n)
            ->delete($n, true);

        $this->assertStringMatchesFormat('MATCH (%s) DETACH DELETE %s', $query->toQuery());
    }

    public function testProveThatMovieGraphIsGone(): void
    {
        $n = node();

        $query = query()
            ->match($n)
            ->returning($n);

        $this->assertStringMatchesFormat('MATCH (%s) RETURN %s', $query->toQuery());
    }
}
